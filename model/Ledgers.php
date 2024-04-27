<?php
class Ledgers{

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
            $stmt = $this->conn->query("SHOW TABLES LIKE 'ledgers'");
            $tableExists = $stmt->rowCount() > 0;

            if (!$tableExists) {
                $sql = "CREATE TABLE IF NOT EXISTS ledgers (
                    Id INT AUTO_INCREMENT PRIMARY KEY,
                    Name VARCHAR(255) NOT NULL,
                    Series INT NOT NULL,
                    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
                $this->conn->exec($sql);
            }
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }


    public function createLedger($name, $series) {
        $name = isset($name) ? $name : '';
        $series = !empty($series) ? $series : '';
        try {

                $sql = "INSERT INTO ledgers (Name, Series)
                VALUES ('$name', $series)";

                $this->conn->query($sql);
            $message = "Ledger created Successfully.";
            $status = true;
        } catch (PDOException $e) {
            $message = "Ledger creation failed: " . $e->getMessage();
            $status = false;
        } catch (Exception $e) {
            $message = $e->getMessage();
            $status = false;
        }
    
        $response = array(
            'status' => $status,
            'status_message' => $message
        );
    
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function getAllLedgers(){
        $sql = "SELECT * FROM ledgers";
        // $data = $this->conn->query($sql);
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
    
        // Fetch ledger data
        $ledgerData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $message = "Successfully Get All Ledgers.";
		$status = true;		

        $response = array(
            'status' => $status,
            'status_message' => $message,
            'data' => $ledgerData
        );

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function updateLedgerById($id, $name, $series) {
        $name = isset($name) ? $name : '';
        $series = isset($series) ? $series : '';
    
        try {
            // Check if the ledger with the given ID exists
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM ledgers WHERE Id = ?");
            $stmt->execute([$id]);
            $ledgerExists = $stmt->fetchColumn() > 0;
    
            if ($ledgerExists) {
                // Ledger exists, so prepare the update query
                $sql = "UPDATE ledgers SET ";
                $params = [];
                if (!empty($name)) {
                    $sql .= "Name = ?, ";
                    $params[] = $name;
                }
                if (!empty($series)) {
                    $sql .= "Series = ?, ";
                    $params[] = $series;
                }

                // Remove the trailing comma and space
                $sql = rtrim($sql, ', ');
                $sql .= " WHERE Id = ?";
                $params[] = $id;
    
                // Execute the update query
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($params);
    
                $message = "Ledger details updated successfully.";
                $status = true;
            } else {
                $message = "Ledger not found in DB.";
                $status = false;
            }
        } catch (PDOException $e) {
            $message = "Ledger update failed: " . $e->getMessage();
            $status = false;
        } catch (Exception $e) {
            $message = $e->getMessage();
            $status = false;
        }
    
        $response = array(
            'status' => $status,
            'status_message' => $message
        );
    
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
    public function getLedgers($page = 1, $limit = 10, $search = null, $sortBy = 'CreatedAt', $sortOrder = 'DESC', $fromDate = null, $toDate = null) {
        // Initialize variables to store SQL conditions and parameters
        $conditions = array();
        $params = array();
    
        // Base SQL query
        $sql = "SELECT * FROM ledgers ";
    
        // Handle search
        if ($search !== null) {
            $conditions[] = "(Name LIKE ? OR Series LIKE ?)";
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
    
        // Fetch ledger data
        $ledgerData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Total count of ledgers is not required in this case
        $totalCount = $this->getTotalLedgerCount($search, $fromDate, $toDate);
        
        // Calculate total pages
        $totalPages = ceil($totalCount / $limit);
    
        // Prepare response data
        $response = array(
            'data' => $ledgerData,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages
        );
    
        // Set response header to indicate JSON content
        header('Content-Type: application/json');
    
        // Encode response data as JSON and echo it
        echo json_encode($response);
    }

    public function getTotalLedgerCount($search = null, $fromDate = null, $toDate = null) {
        // Initialize variables to store SQL conditions and parameters
        $conditions = array();
        $params = array();
    
        // Base SQL query
        $sql = "SELECT COUNT(*) AS total FROM ledgers";
    
        // Handle search
        if ($search !== null) {
            $conditions[] = "(Name LIKE ? OR Series LIKE ?)";
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
    
    public function deleteLedger($ledgerId) {		
		if($ledgerId) {			
			$sql = "
				DELETE FROM ledgers 
				WHERE Id = '".$ledgerId."'";	 
			if($this->conn->exec($sql)) {
				$messgae = "Ledger delete Successfully.";
				$status = true;			
			} else {
				$messgae = "Ledger delete failed.";
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