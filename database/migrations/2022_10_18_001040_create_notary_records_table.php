<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\CookieIdentifier;
use App\Models\DocumentType;

class CreateNotaryRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notary_records', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CookieIdentifier::class);
            $table->foreignIdFor(DocumentType::class);
            $table->string('first_name',50);
            $table->string('last_name',100);
            $table->string('email',70);
            $table->date('date');
            $table->timestamps();
            $table->index('date');
            $table->index(['first_name','last_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notary_records');
    }
}
