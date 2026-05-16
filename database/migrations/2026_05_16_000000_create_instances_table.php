<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('instances', function (Blueprint $table) {
            $table->id();
            $table->string('subdomain', 32)->unique();
            $table->string('email');
            $table->string('association_name')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->enum('status', ['pending', 'active', 'suspended', 'deleted'])->default('pending');
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('instances');
    }
};
