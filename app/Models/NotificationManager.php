<?php

namespace App\Models;

use \PDO;
use \DateTime;
use App\Models\Notification;

class NotificationManager
{
	protected $notificationAdapter;
	protected $DB_REQ;

	public function __construct(PDO $DB_REQ)
	{
		$this->DB_REQ = $DB_REQ;
	}

	public function isDoublicate(Notification $notification) {
		$DB_REQ = $this->DB_REQ->prepare('
			SELECT COUNT(*) AS count
			FROM notifications
			WHERE (id_owner = :id_owner, id_sender = :id_sender, type = :type)
			');

		$DB_REQ->bindValue(':id_owner', $notification->owner());
		$DB_REQ->bindValue(':id_sender', $notification->sender());
		$DB_REQ->bindValue(':type', $notification->type());

		$DB_REQ->execute();

		if (intval($result['count']) > 0) {
			return TRUE;
		}

		return FALSE;
	}

	public function add(Notification $notification) {

		if (!(self::isDoublicate($notification))) {

			$DB_REQ = $this->DB_REQ->prepare('
				INSERT INTO notifications(id_owner, id_sender, unread, type, id_reference, date_notif)
				VALUES (:id_owner, :id_sender, :unread, :type, :id_reference, NOW())
				');
			
			$DB_REQ->bindValue(':id_owner', $notification->owner());
			$DB_REQ->bindValue(':id_sender', $notification->sender());
			$DB_REQ->bindValue(':unread', $notification->unread());
			$DB_REQ->bindValue(':type', $notification->type());
			$DB_REQ->bindValue(':id_reference', $notification->referenceId());

			$DB_REQ->execute();
		}
		else {

			$notification->setUnread(TRUE);
			self::update($notification);
		}
	}

	public function markRead(array $notifications) {
		if ($notification->unread() === TRUE) {
			$notification->setUnread(FALSE);
		}
	}

	public function update(Notification $notification) {
		$DB_REQ = $this->DB_REQ->prepare('
			UPDATE notifications
			SET
				id_owner = :id_owner,
				id_sender = :id_sender,
				unread = :unread,
				type = :type,
				id_reference = :id_reference,
				date_notif = NOW()
			WHERE id = :id
			');
		$DB_REQ->bindValue(':id_owner', $notification->owner());
		$DB_REQ->bindValue(':id_sender', $notification->sender());
		$DB_REQ->bindValue(':type', $notification->type());
		$DB_REQ->bindValue(':id_reference', $notification->referenceId());

		$DB_REQ->execute();
	}

	// public function get(User $user, $limit = 20, $offset = 0) : array;
}
