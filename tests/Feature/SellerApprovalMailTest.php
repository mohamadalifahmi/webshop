<?php

use App\Mail\SellerApprovedMail;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

it('sends a congrats email to the correct seller', function () {
    Mail::fake();

    $seller = Seller::factory()->for(User::factory())->create([
        'store_name' => 'Ahmed Electronics',
        'status' => 'approved',
    ]);

    Mail::to($seller->user->email)->queue(new SellerApprovedMail($seller->load('user')));

    Mail::assertQueued(SellerApprovedMail::class, function (SellerApprovedMail $mail) use ($seller) {
        return $mail->hasTo($seller->user->email) && $mail->seller->is($seller);
    });
});

it('puts the store name in the subject', function () {
    $seller = Seller::factory()->for(User::factory())->create(['store_name' => 'Maya Boutique']);

    $mail = new SellerApprovedMail($seller->load('user'));
    $subject = $mail->envelope()->subject;

    expect($subject)->toContain('Maya Boutique');
});
