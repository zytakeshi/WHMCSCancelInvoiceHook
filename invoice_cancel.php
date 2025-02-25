<?php
// Simple invoice cancellation hook
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

// Add cancel button to invoice view
add_hook('ClientAreaPageViewInvoice', 1, function($vars) {
    global $_LANG;
    
    if ($vars['status'] == 'Unpaid') {
        $invoiceId = (int)$vars['invoiceid'];
        $cancelUrl = 'viewinvoice.php?id=' . $invoiceId . '&action=cancelinvoice';
        $cancelButton = '<a href="' . $cancelUrl . '" class="btn btn-danger" style="margin-top: 10px;" onclick="return confirm(\'' . $_LANG['invoicecancelconfirmation'] . '\');">' . $_LANG['invoicecancellation'] . '</a>';
        
        return ['cancelButton' => $cancelButton];
    }
    return [];
});

// Process cancellation action
if (isset($_GET['action']) && $_GET['action'] == 'cancelinvoice' && isset($_GET['id'])) {
    $invoiceId = (int)$_GET['id'];
    $userId = $_SESSION['uid'];
    
    // Check if invoice belongs to user and is unpaid
    $invoice = Capsule::table('tblinvoices')
        ->where('id', $invoiceId)
        ->where('userid', $userId)
        ->where('status', 'Unpaid')
        ->first();
    
    if ($invoice) {
        // Update invoice status
        Capsule::table('tblinvoices')
            ->where('id', $invoiceId)
            ->update(['status' => 'Cancelled']);
        
        // Log the action
        logActivity("Invoice #$invoiceId cancelled by client", $userId);
        
        // Redirect with success message
        header("Location: viewinvoice.php?id=$invoiceId&cancelled=1");
        exit;
    }
    
    // Invalid invoice
    header("Location: viewinvoice.php?id=$invoiceId&cancelled=0");
    exit;
}
