<?php
class LossOfPointers{

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
            $stmt = $this->conn->query("SHOW TABLES LIKE 'lossofpointers'");
            $tableExists = $stmt->rowCount() > 0;

            if (!$tableExists) {
                $sql = "CREATE TABLE IF NOT EXISTS lossofpointers (
                    Id INT AUTO_INCREMENT PRIMARY KEY,
                    DV_No VARCHAR(50) NOT NULL,
                    DV_Date DATE NOT NULL,
                    Bill_Description VARCHAR(255) NOT NULL,
                    Amount VARCHAR(255) NOT NULL,
                    Beneficiary_Name VARCHAR(100) NOT NULL,
                    IFSC_Code VARCHAR(20) NOT NULL,
                    Account_No VARCHAR(20) NOT NULL,
                    Remark VARCHAR(255) NOT NULL,
                    Cheque_No VARCHAR(20) NOT NULL,
                    Value_Date DATE NOT NULL,
                    Release_Date DATE NOT NULL,
                    UTR_No VARCHAR(50) NOT NULL,
                    Rejected_Reason VARCHAR(255) NOT NULL,
                    Status VARCHAR(20) NOT NULL,
                    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
                $this->conn->exec($sql);
            }
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function createLossOfPointers($dvNo, $dvDate, $billDescription, $amount, $beneficiaryName, $ifscCode, $accountNo, $remark, $chequeNo, $valueDate, $releaseDate, $utrNo, $rejectedReason, $status) {
        try {
                $sql = "INSERT INTO lossofpointers (DV_No, DV_Date, Bill_Description, Amount, Beneficiary_Name, IFSC_Code, Account_No, Remark, Cheque_No, Value_Date, Release_Date, UTR_No, Rejected_Reason, Status)
                VALUES ('$dvNo', '$dvDate', '$billDescription', '$amount', '$beneficiaryName', '$ifscCode', '$accountNo', '$remark', '$chequeNo', '$valueDate', '$releaseDate', '$utrNo', '$rejectedReason', '$status')";
                $this->conn->query($sql);
            $message = "LOP created Successfully.";
            $status = true;
        } catch (PDOException $e) {
            $message = "LOP creation failed: " . $e->getMessage();
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

    public function getCompletedLossOfPointers($page = 1, $limit = 10, $search = null, $sortBy = 'CreatedAt', $sortOrder = 'DESC', $fromDate = null, $toDate = null) {
        // Initialize variables to store SQL conditions and parameters
        $conditions = array();
        $params = array();
    
        // Base SQL query
        $sql = "SELECT 
        DV_No,
        DV_Date, 
        COUNT(*) AS `Total_Records`,
        SUM(Amount) AS `Total_Amount`,
        SUM(CASE WHEN Status = 'SUCCESS' THEN 1 ELSE 0 END) AS `Total_Success_Records`,
        SUM(CASE WHEN Status = 'SUCCESS' THEN Amount ELSE 0 END) AS `Total_Success_Amount`,
        SUM(CASE WHEN Status = 'FAILED' THEN 1 ELSE 0 END) AS `Total_Failed_Records`,
        SUM(CASE WHEN Status = 'FAILED' THEN Amount ELSE 0 END) AS `Total_Failed_Amount`
        FROM lossofpointers";
    
            $conditions[] = "(Status LIKE ? OR Status LIKE ?)";
            $params[] = "SUCCESS";
            $params[] = "FAILED";

        // Handle search
        if ($search !== null) {
            $conditions[] = "(DV_No LIKE ?)";
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
    
        // Group by DV_No
        $sql .= " GROUP BY DV_No, DV_Date";
    
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
        $totalCount = $this->getCompletedTotalLossOfPointersCount($search, $fromDate, $toDate);
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
    
    public function getCompletedTotalLossOfPointersCount($search = null, $fromDate = null, $toDate = null) {
        // Initialize variables to store SQL conditions and parameters
        $conditions = array();
        $params = array();
    
        // Base SQL query
        $sql = "SELECT COUNT(*) AS total FROM lossofpointers";
        
        $conditions[] = "(Status LIKE ? OR Status LIKE ?)";
        $params[] = "SUCCESS";
        $params[] = "FAILED";

        // Handle search
        if ($search !== null) {
            $conditions[] = "(DV_No LIKE ?)";
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

        // Group by DV_No
        $sql .= " GROUP BY DV_No, DV_Date";
    
        // Prepare and execute SQL query
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
    
        // Fetch total count
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Return total count
        return count($result);
    }

    public function getPendingLossOfPointers($page = 1, $limit = 10, $search = null, $sortBy = 'CreatedAt', $sortOrder = 'DESC', $fromDate = null, $toDate = null) {
        // Initialize variables to store SQL conditions and parameters
        $conditions = array();
        $params = array();
    
        // Base SQL query
        $sql = "SELECT 
        DV_No,
        DV_Date, 
        COUNT(*) AS `Total_Records`, 
        SUM(Amount) AS `Total_Amount`, 
        SUM(CASE WHEN IFSC_Code = 'SBIB000175' THEN 1 ELSE 0 END) AS `TPTY_Records`, 
        SUM(CASE WHEN IFSC_Code = 'SBIB000175' THEN Amount ELSE 0 END) AS `TPTY_Amount`, 
        SUM(CASE WHEN IFSC_Code != 'SBIB000175' AND Amount < 200000 THEN 1 ELSE 0 END) AS `NEFT_Records`, 
        SUM(CASE WHEN IFSC_Code != 'SBIB000175' AND Amount < 200000 THEN Amount ELSE 0 END) AS `NEFT_Amount`, 
        SUM(CASE WHEN IFSC_Code != 'SBIB000175' AND Amount >= 200000 THEN 1 ELSE 0 END) AS `RTGS_Records`, 
        SUM(CASE WHEN IFSC_Code != 'SBIB000175' AND Amount >= 200000 THEN Amount ELSE 0 END) AS `RTGS_Amount` 
        FROM lossofpointers";
    
            $conditions[] = "(Status LIKE ?)";
            $params[] = "PENDING";

        // Handle search
        if ($search !== null) {
            $conditions[] = "(DV_No LIKE ?)";
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
    
        // Group by DV_No
        $sql .= " GROUP BY DV_No, DV_Date";
    
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
        $totalCount = $this->getPendingTotalLossOfPointersCount($search, $fromDate, $toDate);
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
    
    public function getPendingTotalLossOfPointersCount($search = null, $fromDate = null, $toDate = null) {
        // Initialize variables to store SQL conditions and parameters
        $conditions = array();
        $params = array();
    
        // Base SQL query
        $sql = "SELECT COUNT(*) AS total FROM lossofpointers";
        
        $conditions[] = "(Status LIKE ?)";
        $params[] = "PENDING";

        // Handle search
        if ($search !== null) {
            $conditions[] = "(DV_No LIKE ?)";
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

        // Group by DV_No
        $sql .= " GROUP BY DV_No, DV_Date";
    
        // Prepare and execute SQL query
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
    
        // Fetch total count
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Return total count
        return count($result);
    }

    public function getLossOfPointers($page = 1, $limit = 10, $search = null, $sortBy = 'CreatedAt', $sortOrder = 'DESC', $fromDate = null, $toDate = null) {
        // Initialize variables to store SQL conditions and parameters
        $conditions = array();
        $params = array();
    
        // Base SQL query
        $sql = "SELECT 
        DV_No,
        COUNT(*) AS Total_Count,
        SUM(CASE WHEN IFSC_Code = 'SBIB000175' THEN 1 ELSE 0 END) AS SBI_Total_Count,
        SUM(CASE WHEN IFSC_Code <> 'SBIB000175' AND CAST(Amount AS UNSIGNED) < 200000 THEN 1 ELSE 0 END) AS ITGS_Total_Count,
        SUM(CASE WHEN IFSC_Code <> 'SBIB000175' AND CAST(Amount AS UNSIGNED) >= 200000 THEN 1 ELSE 0 END) AS MTGS_Total_Count 
        FROM lossofpointers";
    
        // Handle search
        if ($search !== null) {
            $conditions[] = "(DV_No LIKE ?)";
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
    
        // Group by DV_No
        $sql .= " GROUP BY DV_No";
    
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
        $totalCount = $this->getTotalLossOfPointersCount($search, $fromDate, $toDate);
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
    
    public function getTotalLossOfPointersCount($search = null, $fromDate = null, $toDate = null) {
        // Initialize variables to store SQL conditions and parameters
        $conditions = array();
        $params = array();
    
        // Base SQL query
        $sql = "SELECT COUNT(*) AS total FROM lossofpointers";
    
        // Handle search
        if ($search !== null) {
            $conditions[] = "(DV_No LIKE ?)";
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

        // Group by DV_No
        $sql .= " GROUP BY DV_No";
    
        // Prepare and execute SQL query
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
    
        // Fetch total count
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Return total count
        return count($result);
    }
    

    
}

?>