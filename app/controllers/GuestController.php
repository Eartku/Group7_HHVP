<?php
class GuestController extends Controller {
// GuestController.php
public function index(): void {
    $this->view('guest', [
        'noLayout' => true  // ← truyền từ đây
    ]);
}
}
