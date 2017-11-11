<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table){

            $table->dropForeign([
                'user_id'
            ]);

            $table->dropForeign([
                'category_id'
            ]);

            $table->dropIndex('posts_user_id_foreign');

            $table->dropIndex('posts_category_id_foreign');

            $table->dropColumn([
                'user_id',
                'category_id'
            ]);
        });
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
