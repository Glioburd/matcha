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
			WHERE id_owner = :id_owner AND id_sender = :id_sender AND type = :type
			');

		$DB_REQ->bindValue(':id_owner', $notification->owner());
		$DB_REQ->bindValue(':id_sender', $notification->sender());
		$DB_REQ->bindValue(':type', $notification->type());

		$DB_REQ->execute();
		$result = $DB_REQ->fetch(PDO::FETCH_ASSOC);

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
			// $DB_REQ = $this->DB_REQ->prepare('
			// 	SELECT * FROM notifications
			// 	WHERE id 
			// 	')
			$notification->setUnread(TRUE);
			self::update($notification);
		}
	}

	public function setAllNotifsAsRead($userId) {
		$DB_REQ = $this->DB_REQ->prepare('
			UPDATE notifications
			SET
				unread = 0
			WHERE id_owner = :id_owner
			');
		$DB_REQ->bindValue(':id_owner', $userId);
		$DB_REQ->execute();	
	}

	public function postCheckUnread()

	public function update(Notification $notification) {
		$DB_REQ = $this->DB_REQ->prepare('
			UPDATE notifications
			SET
				unread = :unread,
				date_notif = NOW()
			WHERE id_owner = :id_owner AND id_sender = :id_sender AND type = :type
			');

		$DB_REQ->bindValue(':id_owner', $notification->owner());
		$DB_REQ->bindValue(':id_sender', $notification->sender());
		$DB_REQ->bindValue(':type', $notification->type());
		$DB_REQ->bindValue(':unread', 1);


		// $DB_REQ->bindValue(':id_reference', $notification->referenceId());

		$DB_REQ->execute();
	}

	public function get(User $user, $max = 20, $offset = 0) {

		$DB_REQ = $this->DB_REQ->prepare('
			SELECT notifications.id, notifications.id_owner, id_sender, unread, type, date_notif, users.login AS loginSender, pictures.src AS pictureSender
			FROM notifications
			INNER JOIN users
			ON users.id = id_sender
			INNER JOIN pictures
			ON pictures.id_owner = id_sender
			WHERE notifications.id_owner = :id_owner
			AND pictures.ismainpic = 1
			ORDER BY date_notif DESC, type
			LIMIT :max OFFSET :offset
			;');
		$DB_REQ->bindValue(':id_owner', (int) $user->id(), PDO::PARAM_INT);
		$DB_REQ->bindValue(':max', (int) $max, PDO::PARAM_INT);
		$DB_REQ->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

		$DB_REQ->execute();

		$data = $DB_REQ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\models\Notification');

		// $UserManagerPDO = new UserManagerPDO($this->DB_REQ);
		// foreach ($data as &$value) {
		// 	$value['id_sender'] = $UserManagerPDO->getUnique($value['id_sender']);
		// }
		// debug($data);
		// die();
		return $data;
	}
}
