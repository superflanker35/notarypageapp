<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\DocumentType;

class FillDocumentTypesTable extends Migration
{
	public $data = [
		['name' => 'доверенность'],
		['name' => 'наследство'],
		['name' => 'справка'],
	];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	DocumentType::insert($this->data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
