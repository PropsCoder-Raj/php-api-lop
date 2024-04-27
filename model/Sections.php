<?php
class Sections{

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
            $stmt = $this->conn->query("SHOW TABLES LIKE 'sections'");
            $tableExists = $stmt->rowCount() > 0;

            if (!$tableExists) {
                $sql = "CREATE TABLE IF NOT EXISTS sections (
                    Id INT AUTO_INCREMENT PRIMARY KEY,
                    Section_Code INT NOT NULL,
                    Section_Name VARCHAR(255) NOT NULL,
                    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
                $this->conn->exec($sql);
            }
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function createSection($section_name, $section_code){ 		
        $section_name = isset($section_name) ? $section_name : '';
        $section_code = isset($section_code) ? $section_code : '';
    
        try {
            $sql = "INSERT INTO sections (Section_Name, Section_Code)
                    VALUES ('$section_name', '$section_code')";
    
            $this->conn->query($sql);
            $message = "Section created Successfully.";
            $status = true;
        } catch (PDOException $e) {
            $message = "Section creation failed.";
            $status = false;
        }
    
        $response = array(
            'status' => $status,
            'status_message' => $message
        );
    
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function updateSectionById($id, $section_name, $section_code){
        $section_name = isset($section_name) ? $section_name : '';
        $section_code = isset($section_code) ? $section_code : '';
    
        try {
            // Check if the user with the given ID exists
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM sections WHERE Id = ?");
            $stmt->execute([$id]);
            $sectionExists = $stmt->fetchColumn() > 0;
    
            if ($sectionExists) {
                // User exists, so prepare the update query
                $sql = "UPDATE sections SET ";
                $params = [];
                if (!empty($section_name)) {
                    $sql .= "Section_Name = ?, ";
                    $params[] = $section_name;
                }
                if (!empty($section_code)) {
                    $sql .= "Section_Code = ?, ";
                    $params[] = $section_code;
                }
                // Remove the trailing comma and space
                $sql = rtrim($sql, ', ');
                $sql .= " WHERE Id = ?";
                $params[] = $id;
    
                // Execute the update query
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($params);
                $message = "Section details updated successfully.";
                $status = true;
            }else{
                $message = "Section not found in DB.";
                $status = false;
            }
        } catch (PDOException $e) {
            $message = "Section updatation failed.";
            $status = false;
        }
    
        $response = array(
            'status' => $status,
            'status_message' => $message
        );
    
        
        $status == false ? header("HTTP/1.0 400 Bad Request") : header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function getAllSections(){
        $sql = "SELECT * FROM sections";
        // $data = $this->conn->query($sql);
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
    
        // Fetch ledger data
        $ledgerData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $message = "Successfully Get All Sections.";
		$status = true;		

        $response = array(
            'status' => $status,
            'status_message' => $message,
            'data' => $ledgerData
        );

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function getSections($page = 1, $limit = 10, $search = null, $sortBy = 'CreatedAt', $sortOrder = 'DESC', $fromDate = null, $toDate = null) {
        // Initialize variables to store SQL conditions and parameters
        $conditions = array();
        $params = array();
    
        // Base SQL query
        $sql = "SELECT * FROM sections";
    
        // Handle search
        if ($search !== null) {
            $conditions[] = "(Section_Name LIKE ? OR Section_Code LIKE ?)";
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
        $sql = "SELECT COUNT(*) AS total FROM sections";
    
        // Handle search
        if ($search !== null) {
            $conditions[] = "(Section_Name LIKE ? OR Section_Code LIKE ?)";
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

    public function deleteSection($sectionId) {		
		if($sectionId) {			
			$sql = "
				DELETE FROM sections 
				WHERE Id = '".$sectionId."'";	 
			if($this->conn->exec($sql)) {
				$messgae = "Section delete Successfully.";
				$status = true;			
			} else {
				$messgae = "Section delete failed.";
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