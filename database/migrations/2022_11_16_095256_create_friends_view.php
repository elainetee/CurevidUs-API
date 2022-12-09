<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Staudenmeir\LaravelMergedRelations\Facades\Schema;
use App\Models\User;

class CreateFriendsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::createMergeView(
            'friends_view',
            [(new User())->acceptedFriendsTo(), (new User())->acceptedFriendsFrom()]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
}
