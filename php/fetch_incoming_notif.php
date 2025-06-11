<?php
include ('../session.php');
include('../assets/connection/sqlconnection.php');

try {
    $notifReceiever = ($_SESSION["role"] == "admin") ? "admin" : (int)$_SESSION["section"];

    $sql = "SELECT * FROM ppmp_notification WHERE notifReceiver=? ORDER BY isRead ASC, created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$notifReceiever]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group notifications by orderID
    $groupedNotifs = [];
    foreach ($data as $notif) {
        $groupedNotifs[$notif['orderID']][] = $notif;
    }

    $filteredNotifs = [];

    foreach ($groupedNotifs as $orderID => $notifs) {
        $cancelledNotif = null;

        foreach ($notifs as $notif) {
            if ($notif['notifStatus'] === 'cancelled') {
                $cancelledNotif = $notif;
                break; // Only one cancelled is enough
            }
        }

        if ($cancelledNotif) {
            $filteredNotifs[] = $cancelledNotif; // Only add cancelled
        } else {
            foreach ($notifs as $notif) {
                $filteredNotifs[] = $notif; // Add all if there's no cancelled
            }
        }
    }

    // Sort: unread first, then newest first
    usort($filteredNotifs, function ($a, $b) {
        if ($a['isRead'] === $b['isRead']) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        }
        return $a['isRead'] - $b['isRead'];
    });

    // Count only unread notifications
    $notifCount = count(array_filter($filteredNotifs, fn($n) => $n['isRead'] == 0));

    $notifHtml = "";

    foreach ($filteredNotifs as $notif) {
        $dateTime = new DateTime($notif['created_at']);
        $formattedTime = $dateTime->format("F j, Y g:ia");

        $notifTitle = "";
        $iconValue = "";

        switch ($notif['notifStatus']) {
            case "incoming_request":
                $notifTitle = "Incoming Request";
                $iconValue = '<i class="fa-solid fa-clipboard-list"></i>';
                break;
            case "updated":
                $notifTitle = "Update Request";
                $iconValue = '<i class="fa-solid fa-arrows-rotate"></i>';
                break;
            case "rejected":
                $notifTitle = "Rejected Request";
                $iconValue = '<i class="fa-solid fa-circle-xmark"></i>';
                break;
            case "approved":
                $notifTitle = "Approved Request";
                $iconValue = '<i class="fa-solid fa-circle-check"></i>';
                break;
            case "cancelled":
                $notifTitle = "Cancelled Request";
                $iconValue = '<i class="fa-solid fa-ban"></i>';
                break;
        }

        $rowClass = ($notif['isRead'] === 0) ? 'unread' : 'read';

        $notifHtml .= '
            <div class="navbar-notif-row ' . $rowClass . '">
                ' . $iconValue . '
                <div class="navbar-notif-main-container">
                    <span class="navbar-notif-time">' . $formattedTime . '</span>
                    <div class="navbar-notif-sub-main-container">
                        <span class="navbar-notif-title">' . $notifTitle . '</span>
                        <span class="navbar-notif-desc">' . $notif['notifMessage'] . '</span>    
                    </div>
                </div>
            </div>
        ';
    }

    echo json_encode([
        "html" => $notifHtml,
        "count" => $notifCount,
        "data" => $filteredNotifs,
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

?>