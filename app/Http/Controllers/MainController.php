<?php

namespace App\Http\Controllers;

use App\Models\CookieIdentifier;
use App\Models\DocumentType;
use App\Models\NotaryRecord;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class MainController extends Controller
{
	private $appCookieName = 'notaryapp_uid';
	private $appCookieVal;
	private $appCookieId;

    public function __construct()
    {
        $this->middleware('guest');
        $this->init();
    }

    private function init()
    {
	    $appCookieVal = Cookie::get($this->appCookieName);

	    if(!$appCookieVal){
	    	$cookieVal = $this->setAppCookie($this->appCookieName);

		    $cookieIdentifier = new CookieIdentifier();
			$cookieIdentifier->value      = $cookieVal;
		    $cookieIdentifier->created_at = date('Y-m-d H:i:s');
		    $cookieIdentifier->save();

		    $this->appCookieVal = $cookieVal;
		    $this->appCookieId = $cookieIdentifier->id;
	    }else{
		    $this->appCookieVal = Cookie::get($this->appCookieName);
		    $this->appCookieId = CookieIdentifier::where('value',Cookie::get('notaryapp_uid'))->value('id');
	    }
    }

    public function showNotaryForm()
    {
    	/*echo $this->appCookieName;
    	echo $this->appCookieId;*/
    	//echo CookieIdentifier::where('value',Cookie::get('notaryapp_uid'))->value('id');
	    $notaryRecords = null;
		$appCookieId = CookieIdentifier::where('value',Cookie::get('notaryapp_uid'))->value('id');
	    if($appCookieId){
	    	$notaryRecords = NotaryRecord::where('cookie_identifier_id', $appCookieId);
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

		$notaryRecord = new NotaryRecord();
		$notaryRecord->cookie_identifier_id = CookieIdentifier::where('value',Cookie::get('notaryapp_uid'))->value('id');
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
    	$cookieVal = Str::random(16);
	    $minutes = 5;
	    Cookie::queue($cookieName, $cookieVal, $minutes);

	    return $cookieVal;
    }
}
