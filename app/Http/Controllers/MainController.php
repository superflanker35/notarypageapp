<?php

namespace App\Http\Controllers;

use App\Models\CookieIdentifier;
use App\Models\DocumentType;
use App\Models\NotaryRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class MainController extends Controller
{
	private $appCookieName = 'notaryapp_uid';
	private $appCookieValue;

    public function __construct()
    {
        $this->middleware('guest');
        $this->init();
    }

    private function init()
    {
	    $aCookieVal = Cookie::get($this->appCookieName);

	    if(!$aCookieVal){
	    	$cookieVal = $this->setAppCookie($this->appCookieName);
		    $cookieIdentifier = new CookieIdentifier();
			$cookieIdentifier->value      = $cookieVal;
		    $cookieIdentifier->created_at = date('Y-m-d H:i:s');
		    $cookieIdentifier->save();
	    }
    }

    public function showNotaryForm()
    {
	    $notaryRecords = '';
		$aCookieId = CookieIdentifier::where('value',Cookie::get($this->appCookieName))->value('id');
	    if($aCookieId){
		    $notaryRecords = DB::table('notary_records')
			    ->leftJoin('document_types', 'notary_records.document_type_id', '=', 'document_types.id')
			    ->select('notary_records.*','document_types.name')
			    ->where('notary_records.cookie_identifier_id','=',$aCookieId)
			    ->get();
	    }

	    $documentTypes = DocumentType::all();

    	if(View::exists('notary')){
    		return view('notary', compact('documentTypes', 'notaryRecords'));
	    }
    }

    public function processNotaryForm(Request $request)
    {                                                            
	    $this->validate($request,
		    [
			    'first_name'    => 'required',
			    'last_name'     => 'required',
			    'email'         => 'required',
			    'document_type' => 'required',
			    'date'          => 'required',
	        ]
	    );

		$pCookieVal = !empty(Cookie::get($this->appCookieName)) ? Cookie::get($this->appCookieName) : $this->getAppCookieVal();

		$notaryRecord = new NotaryRecord();
		$notaryRecord->cookie_identifier_id = CookieIdentifier::where('value',$pCookieVal)->value('id');
		$notaryRecord->first_name           = $request->first_name;
	    $notaryRecord->last_name            = $request->last_name;
	    $notaryRecord->email                = $request->email;
	    $notaryRecord->document_type_id     = $request->document_type;
	    $notaryRecord->date                 = date('Y-m-d', strtotime($request->date));
	    $notaryRecord->save();

	    return response()->json(
	    	[
	    	    'success' => 'true',
		        'message' => 'Запись принята',
		    ]
	    );
    }

    public function setAppCookie($cookieName)
    {
    	$this->setAppCookieVal(Str::random(16));
    	$cookieVal = $this->getAppCookieVal();
	    $minutes = 5;
	    Cookie::queue($cookieName, $cookieVal, $minutes);

	    return $cookieVal;
    }

    private function setAppCookieVal($value)
    {
    	$this->appCookieValue = $value;
    }

    private function getAppCookieVal()
    {
		return $this->appCookieValue;
    }
}