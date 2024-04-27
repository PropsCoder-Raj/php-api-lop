<?php
class Users{

    private $conn;
    
    public function __construct(){
        // Database connection configuration
        $servername = "localhost"; // MySQL server address
        $username = "admin"; // MySQL username
        $password = ""; // MySQL password

        try {
            // Create a new PDO instance to establish a database connection
            $this->conn = new PDO("mysql:host=$servername;dbname=test", $username, $password);
            
            // Set the PDO error mode to exception to enable error handling
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Check if the table exists
            $stmt = $this->conn->query("SHOW TABLES LIKE 'users'");
            $tableExists = $stmt->rowCount() > 0;

            if (!$tableExists) {
                $sql = "CREATE TABLE IF NOT EXISTS users (
                    Id INT AUTO_INCREMENT PRIMARY KEY,
                    Name VARCHAR(255) NOT NULL,
                    Mobile VARCHAR(15) NOT NULL UNIQUE,
                    Email VARCHAR(255) NOT NULL UNIQUE,
                    Password VARCHAR(255) NOT NULL,
                    Role VARCHAR(50) NOT NULL,
                    Status VARCHAR(50) NOT NULL,
                    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    CONSTRAINT UC_Email UNIQUE (Email),
                    CONSTRAINT UC_Mobile UNIQUE (Mobile)
                )";
                $this->conn->exec($sql);
                $this->defaultAdminCeeated(); // Corrected method call
            }
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function defaultAdminCeeated(){
        try {
            // Check if the users table is empty
            $stmt = $this->conn->query("SELECT COUNT(*) FROM users");
            $isEmpty = ($stmt->fetchColumn() == 0);

            if ($isEmpty) {
                // The users table is empty, so insert the admin user
                $name = 'Admin';
                $mobile = '8754875487';
                $email = 'govt@mailinator.com';
                $password = password_hash('Admin@123', PASSWORD_DEFAULT); // Encrypt the password
                $role = 'ADMIN';

                // Execute the query to insert the admin user
                $this->conn->query("INSERT INTO users (Name, Mobile, Email, Password, Role, Status) VALUES ('$name', '$mobile', '$email', '$password', '$role', '1')");
            }
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function createUser($name, $mobile, $email, $password){ 		
        $name = isset($name) ? $name : '';
        $email = isset($email) ? $email : '';
        $mobile = isset($mobile) ? $mobile : '';
        $password = isset($password) ? $password : '';
    
        try {
            $sql = "INSERT INTO users (Name, Email, Mobile, Password, Role, Status)
                    VALUES ('$name', '$email', '$mobile', '$password', 'USER', '0')";
    
            $this->conn->query($sql);
            $message = "User created Successfully.";
            $status = true;
        } catch (PDOException $e) {
            if ($e->getCode() == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                if(strpos($e->getMessage(), 'Email') !== false){
                    $message = "Email already exists.";
                }else if(strpos($e->getMessage(), 'Mobile') !== false){
                    $message = "Mobile number already exists.";
                }
            } else {
                $message = "User creation failed.";
            }
            $status = false;
        }
    
        $response = array(
            'status' => $status,
            'status_message' => $message
        );
    
        $status == false ? header("HTTP/1.0 400 Bad Request") : header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function updateUserById($id, $name, $email, $mobile, $password, $status){         
        $name = isset($name) ? $name : '';
        $email = isset($email) ? $email : '';
        $mobile = isset($mobile) ? $mobile : '';
        $password = isset($password) ? $password : '';
        $status = isset($status) ? $status : '';
    
        try {
            // Check if the user with the given ID exists
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE Id = ?");
            $stmt->execute([$id]);
            $userExists = $stmt->fetchColumn() > 0;
    
            if ($userExists) {
                // User exists, so prepare the update query
                $sql = "UPDATE users SET ";
                $params = [];
                if (!empty($name)) {
                    $sql .= "Name = ?, ";
                    $params[] = $name;
                }
                if (!empty($email)) {
                    $sql .= "Email = ?, ";
                    $params[] = $email;
                }
                if (!empty($mobile)) {
                    $sql .= "Mobile = ?, ";
                    $params[] = $mobile;
                }
                if (!empty($password)) {
                    $sql .= "Password = ?, ";
                    $params[] = password_hash($password, PASSWORD_DEFAULT);
                }
                if (!empty($status) || $status == 0) {
                    $sql .= "Status = ?, ";
                    $params[] = $status;
                }
                // Remove the trailing comma and space
                $sql = rtrim($sql, ', ');
                $sql .= " WHERE Id = ?";
                $params[] = $id;

                // Execute the update query
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($params);
                $message = "User details updated successfully.";
                $status = true;
            }else{
                $message = "User not found in DB.";
                $status = false;
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                if(strpos($e->getMessage(), 'Email') !== false){
                    $message = "Email already exists.";
                }else if(strpos($e->getMessage(), 'Mobile') !== false){
                    $message = "Mobile number already exists.";
                }
            } else {
                $message = "User updation failed.";
            }
            $status = false;
        }
    
        $response = array(
            'status' => $status,
            'status_message' => $message
        );
    
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function getUsers($page = 1, $limit = 10, $search = null, $sortBy = 'CreatedAt', $sortOrder = 'DESC', $fromDate = null, $toDate = null) {
        // Initialize variables to store SQL conditions and parameters
        $conditions = array();
        $params = array();
    
        // Base SQL query
        $sql = "SELECT * FROM users";
    
        // Handle search
        if ($search !== null) {
            $conditions[] = "(Email LIKE ? OR Name LIKE ? OR Mobile LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
    
        // Handle date range
        if ($fromDate !== null) {
            $conditions[] = "CreatedAt >= ?";
            $params[] = $fromDate;
        }
        if ($toDate !== null) {
            $conditions[] = "CreatedAt <= ?";
            $params[] = $toDate;
        }

        // Add condition for role USER
        $conditions[] = "Role = 'USER'";
    
        // Combine conditions
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        // Handle sorting
        if ($sortBy !== null) {
            $sql .= " ORDER BY $sortBy $sortOrder";
        }
    
        // Calculate offset for pagination
        $offset = ($page - 1) * $limit;
    
        // Add limit and offset to SQL query
        $sql .= " LIMIT $limit OFFSET $offset";

        // Prepare and execute SQL query
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
    
        // Fetch user data
        $userData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Get total count of users (for pagination)
        $totalCount = $this->getTotalUserCount($search, $fromDate, $toDate);
    
        // Calculate total pages
        $totalPages = ceil($totalCount / $limit);
    
        // Prepare response data
        $response = array(
            'data' => $userData,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages
        );
    
        // Set response header to indicate JSON content
        header('Content-Type: application/json');
    
        // Encode response data as JSON and echo it
        echo json_encode($response);
    }
    
    public function getTotalUserCount($search = null, $fromDate = null, $toDate = null) {
        // Initialize variables to store SQL conditions and parameters
        $conditions = array();
        $params = array();
    
        // Base SQL query
        $sql = "SELECT COUNT(*) AS total FROM users";
    
        // Handle search
        if ($search !== null) {
            $conditions[] = "(Email LIKE ? OR Name LIKE ? OR Mobile LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
    
        // Handle date range
        if ($fromDate !== null) {
            $conditions[] = "CreatedAt >= ?";
            $params[] = $fromDate;
        }
        if ($toDate !== null) {
            $conditions[] = "CreatedAt <= ?";
            $params[] = $toDate;
        }
        
        // Add condition for role USER
        $conditions[] = "Role = 'USER'";
    
        // Combine conditions
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        // Prepare and execute SQL query
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
    
        // Fetch total count
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Return total count
        return $result['total'];
    }

    public function getUserWithEmailPassword($email, $password) {
    
        try {
            $sql = "SELECT * FROM users 
                WHERE Email = '$email'";
            // Execute the query
            $result = $this->conn->query($sql);
        
            // Check if the query was executed successfully
            if ($result) {
                // Check if any rows were returned
                if ($result->rowCount() > 0) {
                    $user = $result->fetch(PDO::FETCH_ASSOC);
                    // Verify the password
                    if (password_verify($password, $user['Password'])) {
                        $status = 1;
                        $user['Password'] = null;
                        $data = $user;
                        $message = "Login Successfully.";
                    } else {
                        $status = 0;
                        $data = null;
                        $message = "Password not matched.";
                    }
                } else {
                    // No user found with the given email
                    $status = 0;
                    $data = null;
                    $message = "User not Found.";
                }
            } else {
                // Error occurred during query execution
                $status = 0;
                $data = null;
                $message = "User not Found.";
            }
        
            // Construct response
            $response = array(
                'status' => $status,
                'data' => $data,
                'message' => $message
            );
        
            // Set header and return JSON response
            header('Content-Type: application/json');
            return $response;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    


    public function deleteUser($userId) {		
		if($userId) {			
			$sql = "
				DELETE FROM users 
				WHERE Id = '".$userId."' AND Role = 'USER'";	 
			if($this->conn->exec($sql)) {
				$messgae = "User delete Successfully.";
				$status = true;			
			} else {
				$messgae = "User delete failed.";
				$status = false;			
			}		
		} else {
			$messgae = "Invalid request.";
			$status = false;
		}
		$response = array(
			'status' => $status,
			'status_message' => $messgae
		);
		header('Content-Type: application/json');
		echo json_encode($response);	
	}
    
    
}
?>