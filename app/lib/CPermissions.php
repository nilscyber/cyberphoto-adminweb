<?php

// Admin-CRUD för behörighetssystemet (cyberadmin.employees / permissions / employee_permissions).
// CCheckIP hanterar läsvägen ("har nuvarande användare behörighet X"), den här klassen
// hanterar adminsidans skrivvägar (permissions_admin.php).
Class CPermissions {

	public function getAllEmployees() {
		$db = Db::getConnection(false);
		$sql = "SELECT employee_id, login_mail, name FROM cyberadmin.employees WHERE active = 1 ORDER BY name";
		$res = mysqli_query($db, $sql);
		$out = array();
		while ($res && $row = mysqli_fetch_assoc($res)) { $out[] = $row; }
		return $out;
	}

	public function getAllPermissions() {
		$db = Db::getConnection(false);
		$sql = "SELECT permission_key, description FROM cyberadmin.permissions ORDER BY description";
		$res = mysqli_query($db, $sql);
		$out = array();
		while ($res && $row = mysqli_fetch_assoc($res)) { $out[] = $row; }
		return $out;
	}

	// Returnerar [employee_id][permission_key] => true för varje beviljad kombination
	public function getPermissionMatrix() {
		$db = Db::getConnection(false);
		$sql = "SELECT employee_id, permission_key FROM cyberadmin.employee_permissions";
		$res = mysqli_query($db, $sql);
		$out = array();
		while ($res && $row = mysqli_fetch_assoc($res)) {
			$out[(int)$row['employee_id']][$row['permission_key']] = true;
		}
		return $out;
	}

	// $checked: [employee_id][permission_key] => "1" för ikryssade rutor i formuläret.
	// Sätter hela grid:et (alla kända anställda x alla kända permissions) till exakt detta.
	public function saveMatrix($checked) {
		$db = Db::getConnection(true);

		$employees = $this->getAllEmployees();
		$permissions = $this->getAllPermissions();

		mysqli_begin_transaction($db);
		try {
			foreach ($employees as $emp) {
				$employeeId = (int)$emp['employee_id'];
				foreach ($permissions as $perm) {
					$key = $perm['permission_key'];
					$isChecked = isset($checked[$employeeId][$key]) && $checked[$employeeId][$key] == '1';

					if ($isChecked) {
						$keyEsc = mysqli_real_escape_string($db, $key);
						mysqli_query($db, "INSERT IGNORE INTO cyberadmin.employee_permissions (employee_id, permission_key)
							VALUES ($employeeId, '$keyEsc')");
					} else {
						$keyEsc = mysqli_real_escape_string($db, $key);
						mysqli_query($db, "DELETE FROM cyberadmin.employee_permissions
							WHERE employee_id = $employeeId AND permission_key = '$keyEsc'");
					}
				}
			}
			mysqli_commit($db);
		} catch (Exception $e) {
			mysqli_rollback($db);
			throw $e;
		}
	}

	public function addEmployee($mail, $name) {
		$db = Db::getConnection(true);
		$mailEsc = mysqli_real_escape_string($db, trim($mail));
		$nameEsc = mysqli_real_escape_string($db, trim($name));
		if ($mailEsc === '' || $nameEsc === '') { return; }

		mysqli_query($db, "INSERT INTO cyberadmin.employees (login_mail, name, active)
			VALUES ('$mailEsc', '$nameEsc', 1)
			ON DUPLICATE KEY UPDATE name = VALUES(name), active = 1");
	}

	public function updateEmployeeName($employeeId, $name) {
		$db = Db::getConnection(true);
		$employeeId = (int)$employeeId;
		$nameEsc = mysqli_real_escape_string($db, trim($name));
		if ($employeeId <= 0 || $nameEsc === '') { return; }

		mysqli_query($db, "UPDATE cyberadmin.employees SET name = '$nameEsc' WHERE employee_id = $employeeId");
	}

	// Soft-delete: personen försvinner ur listor/behörighetskontroller men historiken i employee_permissions bevaras.
	public function deactivateEmployee($employeeId) {
		$db = Db::getConnection(true);
		$employeeId = (int)$employeeId;
		if ($employeeId <= 0) { return; }

		mysqli_query($db, "UPDATE cyberadmin.employees SET active = 0 WHERE employee_id = $employeeId");
	}

	public function addPermission($key, $description) {
		$db = Db::getConnection(true);
		$keyEsc = mysqli_real_escape_string($db, trim($key));
		$descEsc = mysqli_real_escape_string($db, trim($description));
		if ($keyEsc === '' || $descEsc === '') { return; }

		mysqli_query($db, "INSERT INTO cyberadmin.permissions (permission_key, description)
			VALUES ('$keyEsc', '$descEsc')
			ON DUPLICATE KEY UPDATE description = VALUES(description)");
	}

	public function updatePermissionDescription($key, $description) {
		$db = Db::getConnection(true);
		$keyEsc = mysqli_real_escape_string($db, trim($key));
		$descEsc = mysqli_real_escape_string($db, trim($description));
		if ($keyEsc === '' || $descEsc === '') { return; }

		mysqli_query($db, "UPDATE cyberadmin.permissions SET description = '$descEsc' WHERE permission_key = '$keyEsc'");
	}

	// Tar bort en behörighet helt. FK ON DELETE CASCADE tar samtidigt bort alla anställdas kryss för den.
	public function deletePermission($key) {
		$db = Db::getConnection(true);
		$keyEsc = mysqli_real_escape_string($db, trim($key));
		if ($keyEsc === '') { return; }

		mysqli_query($db, "DELETE FROM cyberadmin.permissions WHERE permission_key = '$keyEsc'");
	}

}

?>
