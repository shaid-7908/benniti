<?php
include_once $docRoot."lib/Database.php";
include_once $docRoot."lib/Session.php";

/* Solvers operate on public_id */
class Solvers {

  private $db;
  private $solverColumns = "tbl_solvers.id, tbl_solvers.public_id, tbl_solvers.public_id, tbl_solvers.fk_org_id, tbl_solvers.fk_user_id, tbl_solvers.headline, tbl_solvers.abstract, tbl_solvers.experience, tbl_solvers.availability, tbl_solvers.rate, tbl_solvers.locations, tbl_solvers.is_coach, tbl_solvers.allow_external, tbl_solvers.created_at, tbl_solvers.updated_at";

  public function __construct() {
    $this->db = new Database();
  }

  public function getRealId($publicId) {
    if (!isset($publicId) || $publicId == "") {
      error_log("Could not find real id for solvers when public id not set");
      return false;
    }
    $sql = "SELECT id FROM tbl_solvers WHERE public_id = :publicid";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':publicid', $publicId);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    if ($result) {
      return $result[0]->id;
    } else {
      error_log("Could not find real id for solvers with public id " . $publicId);
      return false;
    }
  }

  public function getPublicId($realId) {
    if (!isset($realId) || $realId == "") {
      error_log("Could not find public id for organizations when real id not set");
      return false;
    }
    $sql = "SELECT public_id FROM tbl_solvers WHERE id = :id";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':id', $realId);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    if ($result) {
      return $result[0]->public_id;
    } else {
      error_log("Could not find public id for solver with real id " . $realId);
      return false;
    }
  }

  public function checkSolverExistsInOrg($orgId, $organizations) {
    $orgId = $organizations->getRealId($orgId);
    if ($orgId) {
      $sql = "SELECT id from tbl_solvers WHERE fk_org_id = :orgId";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':orgId', $orgId);
      $stmt->execute();
      if ($stmt->rowCount()> 0) {
        return true;
      } else {
        return false;
      }
    }
    return false;
  }

  public function checkSolverExistsById($solverId) {
    $sql = "SELECT headline from tbl_solvers WHERE public_id = :solverId";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':solverId', $solverId);
    $stmt->execute();
    if ($stmt->rowCount()> 0) {
      return true;
    } else {
      return false;
    }
  }

  // Solver Registration
  public function createSolver($data, $byAdmin, $users, $organizations) {
    $skillIds = $data['skillsIds'];
    $skillIds = rtrim($skillIds, ',');
    $snowflake = new \Godruoyi\Snowflake\Snowflake;
    $newPublicId = $snowflake->id();

    if ( $data['fk_org_id'] == "" || $data['experience'] == "" || $data['availability'] == "" || $data['location'] == "" || $data['headline'] == "") {
      return createUserMessage("error", "All fields are required!");
    }  elseif(strlen($data['experience']) < 50) {
      return createUserMessage("error", "Please provide a detailed description (at least 50 characters) of your experience.");
    } else {
      $sql = "INSERT INTO tbl_solvers(public_id, fk_org_id, fk_user_id, experience,headline, availability, rate,rate_type, location_preference,certificates,city,state,zip,industry,technology,speciality) 
        VALUES(:publicid, :orgid, :userid,  :experience,:headline, :availability, :rate,:rate_type, :location_preference,:certificates,:city,:state,:zip,:industry,:technology,:speciality)";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':publicid', $newPublicId);
      $stmt->bindValue(':userid', $users->getRealId(Session::get("userid")));
      $stmt->bindValue(':orgid', $data['fk_org_id']);
      $stmt->bindValue(':experience', $data['experience']);
      $stmt->bindValue(':headline',$data['headline']);
      $stmt->bindValue(':availability', $data['availability']);
      $stmt->bindValue(':rate', $data['rate']);
      $stmt->bindValue(':rate_type',$data['rate_type']);
      $locationPreference = implode(',', $data['location']);
      $stmt->bindValue(':location_preference', $locationPreference);
      $stmt ->bindValue(':certificates',$data['cretificate_hidden']);
      $stmt->bindValue(':city',$data['city']);
      $stmt->bindValue(':state',$data['state']);
      $stmt->bindValue(':zip',$data['zip']);
      $stmt->bindValue(':speciality',$data['specialty_hidden']);
      $stmt->bindValue(':industry',$data['industry_hidden']);
      $stmt->bindValue(':technology',$data['technology_hidden']);
      $result = $stmt->execute();
      // Retrieve the solver id
           

      if ($result) {
           $solverId = $this->db->pdo->lastInsertId();
            
            // Insert solver locations into solver_locations table
            // for ($i = 0; $i < count($data['city']); $i++) {
            //     $city = $data['city'][$i];
            //     $state = $data['state'][$i];
            //     $zip = $data['zip'][$i];

            //     $sql = "INSERT INTO tbl_solver_locations(fk_solver_id, city, state, zip) 
            //             VALUES(:solverid, :city, :state, :zip)";
            //     $stmt = $this->db->pdo->prepare($sql);
            //     $stmt->bindValue(':solverid', $solverId);
            //     $stmt->bindValue(':city', $city);
            //     $stmt->bindValue(':state', $state);
            //     $stmt->bindValue(':zip', $zip);
            //     $stmt->execute();
            // }
          Session::set('pendingMsg', createUserMessage("success", "Solver Profile created."));
          $result =  $this->updateSkillsForSolver($newPublicId, $skillIds);
          $onboarding=Session::get('onboarding');
          if (isset($onboarding) && isset($onboarding->completedUrl))
            return "<script>location.href='" . $onboarding->completedUrl . "&solverid=" . $newPublicId . "';</script>";
          else
            //return "<script>location.href='solverDetail.php?orgid=" . $organizations->getPublicId($data['fk_org_id']) . "&solverid=" . $newPublicId  . "';</script>";
           return "<script>location.href='solverCreated.php?orgid=" . $organizations->getPublicId($data['fk_org_id']) . "&solverid=" . $newPublicId  . "';</script>";  
      } else {
        return createUserMessage("error", "Solver Profile not created. Something went wrong!");
      }
    }
  }

  //TODO: External callers should get public_id only
  public function getAllSolverData($forExternal=false) {
    $sql = "SELECT $this->solverColumns, tbl_organizations.orgname as orgname 
      FROM tbl_solvers 
      INNER JOIN tbl_organizations ON tbl_solvers.fk_org_id=tbl_organizations.id";
    if ($forExternal)
      $sql .= " WHERE allow_external=1";
    $sql .=";";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $result;
  }

  public function getAllSolverDataForUser($userId, $users, $organizations) {
    $publicUserId = $userId;
    $userId = $users->getRealId($userId);
    if ($userId) {
      $userOrgs = $organizations->getAllOrganizationDataForUser($publicUserId, $users);
      $solverData = [];
      foreach ($userOrgs as $userOrg) {
        $orgSolver = $this->getSolverProfileByOrgIdAndUserId($userOrg->public_id, $publicUserId, $organizations, $users);
        if($orgSolver != ""){
           $orgSolver->orgname = $userOrg->orgname;
        array_push($solverData, $orgSolver);
        }
       
      }
      return $solverData;
    }
    return null;
  }
// When an user have multiple solver data in a single organization
public function getMAllSolverDataForUser($userId, $users, $organizations) {
    $publicUserId = $userId;
    $userId = $users->getRealId($userId);
    if ($userId) {
        $userOrgs = $organizations->getAllOrganizationDataForUser($publicUserId, $users);
        $solverData = [];
        foreach ($userOrgs as $userOrg) {
            $orgSolvers = $this->getAllSolverProfileByOrgIdAndUserId($userOrg->public_id, $publicUserId, $organizations, $users);
            foreach ($orgSolvers as $orgSolver) {
                $orgSolver->orgname = $userOrg->orgname;
                $solverData[] = $orgSolver;
            }
        }
        return $solverData;
    }
    return null;
}


  public function getSolverProfilesByOrgId($publicOrgId, $organizations) {
    $orgId = $organizations->getRealId($publicOrgId);
    if ($orgId) {
      return $this->getSolverProfilesByRealOrgId($orgId);
    }
    return false;
  }

  public function getSolverProfilesByRealOrgId($realSolverId) {
    $sql = "SELECT $this->solverColumns, tbl_organizations.orgname as orgname 
      FROM tbl_solvers 
      INNER JOIN tbl_organizations ON tbl_solvers.fk_org_id=tbl_organizations.id 
      WHERE tbl_organizations.id = :id;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':id', $realSolverId);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    if ($result) {
      return $result;
    } else {
      return false;
    }
  }

  public function getSolverProfileByOrgIdAndUserId($orgId, $userId, $organizations, $users) {
    $orgId = $organizations->getRealId($orgId);
    $userId = $users->getRealId($userId);
    if ($orgId && $userId) {
        $sql = "SELECT $this->solverColumns, tbl_organizations.orgname as orgname 
        FROM tbl_solvers 
        INNER JOIN tbl_organizations ON tbl_solvers.fk_org_id=tbl_organizations.id 
        WHERE tbl_organizations.id = :orgId
        AND tbl_solvers.fk_user_id = :userId 
        LIMIT 1;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':orgId', $orgId);
      $stmt->bindValue(':userId', $userId);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_OBJ);
      if ($result) {
        return $result;
      } else {
        return false;
      }
    }
    return false;
  }
  
   public function getAllSolverProfileByOrgIdAndUserId($orgId, $userId, $organizations, $users) {
    $orgId = $organizations->getRealId($orgId);
    $userId = $users->getRealId($userId);
    if ($orgId && $userId) {
        $sql = "SELECT $this->solverColumns, tbl_organizations.orgname as orgname 
        FROM tbl_solvers 
        INNER JOIN tbl_organizations ON tbl_solvers.fk_org_id=tbl_organizations.id 
        WHERE tbl_organizations.id = :orgId
        AND tbl_solvers.fk_user_id = :userId ;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':orgId', $orgId);
      $stmt->bindValue(':userId', $userId);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_OBJ);
      if ($result) {
        return $result;
      } else {
        return false;
      }
    }
    return false;
  }

  public function getSolverProfileById($solverId){
    $solverId = $this->getRealId($solverId);
    if ($solverId) {
      $sql = "SELECT * FROM tbl_solvers 
        WHERE tbl_solvers.id = :id LIMIT 1;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':id', $solverId);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_OBJ);
      if ($result) {
        return $result;
      } else {
        return false;
      }
    }
    return false;
  }
public function getSolverProfileByRealId($solverId){
   
    if ($solverId) {
      $sql = "SELECT * FROM tbl_solvers 
        WHERE tbl_solvers.id = :id LIMIT 1;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':id', $solverId);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_OBJ);
      if ($result) {
        return $result;
      } else {
        return false;
      }
    }
    return false;
  }

  public function getSolverImageById($solverId, $type="portrait", $raw=false) {
    $solverId = $this->getRealId($solverId);
    if ($solverId) {
      $type = strtolower($type)."Image";
      $sql = "SELECT $type FROM tbl_solvers 
        WHERE tbl_solvers.id = :id LIMIT 1;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':id', $solverId);
      $stmt->execute();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!$raw) {
          echo '<img src="data:image/png;base64,' . base64_encode($row[$type]) . '" width = "50px" height = "50px"/>';
          return;
        }
        else {
          return $row[$type];
        }
      }
      return null;
    }
    return null;
  }

public function getSolverSkillsByID($solver_id){
  $sql ="SELECT *
FROM tbl_solver_skills
WHERE fk_solver_id = :solver_id";
$stmt = $this->db->pdo->prepare($sql);
$stmt->bindValue(':solver_id',$solver_id);
$stmt->execute();
return $stmt->fetchAll(PDO::FETCH_OBJ);
}

 public function getAllLatesSolvers(){
  $sql = "SELECT * FROM tbl_solvers ORDER BY created_at DESC";
  $stmt = $this->db->pdo->prepare($sql);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_OBJ);

 } 
  public function searchSolvers($searchString) {
    $searchStrings = explode(" ", $searchString);
    $results = [];
    foreach ($searchStrings as $str) {
      $keywords = $this->searchSolversByKeyword($str);
      foreach ($keywords as $keyword) {
        array_push($results, $keyword);
      }
      $skills =$this->searchSolversBySkill($str);
      foreach($skills as $skill){
        array_push($results,$skill);
      }
    }
    return $results;
  }
  // Function to search solvers by skill
public function searchSolversBySkill($skillName) {
    $sql = "SELECT tbl_solver_skills.fk_skill_id, tbl_solvers.*, tbl_skills.skill_name,tbl_organizations.orgname as orgname
            FROM tbl_solver_skills
            INNER JOIN tbl_solvers ON tbl_solver_skills.fk_solver_id = tbl_solvers.id
            INNER JOIN tbl_skills ON tbl_solver_skills.fk_skill_id = tbl_skills.id
            INNER JOIN tbl_organizations ON tbl_solvers.fk_org_id=tbl_organizations.id 
            WHERE tbl_skills.skill_name LIKE :skillName";

    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':skillName', '%' . $skillName . '%');
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}



  public function searchSolversByKeyword($keyword, $forExternal=false) {
    $sql = "SELECT $this->solverColumns, tbl_organizations.orgname as orgname 
      FROM tbl_solvers 
      INNER JOIN tbl_organizations ON tbl_solvers.fk_org_id=tbl_organizations.id 
      WHERE tbl_solvers.headline LIKE :headline1
      OR tbl_solvers.experience LIKE :headline2";
    if ($forExternal)
      $sql .= " WHERE allow_external=1";
    $sql .=";";

    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':headline1', "%" . $keyword . "%");
    $stmt->bindValue(':headline2', "%" . $keyword . "%");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function searchSolversByKeywordWithPaging($keyword, $queryTake, $querySkip, $forExternal=false) {
    $take = 10;
    if (is_numeric($queryTake))
      $take = filter_var ($queryTake, FILTER_SANITIZE_NUMBER_INT);
    $skip = 0;
    if (is_numeric($querySkip))
      $skip = filter_var ($querySkip, FILTER_SANITIZE_NUMBER_INT);
    $sql = "SELECT $this->solverColumns, tbl_organizations.orgname as orgname 
      FROM tbl_solvers 
      INNER JOIN tbl_organizations ON tbl_solvers.fk_org_id=tbl_organizations.id 
      WHERE tbl_solvers.headline LIKE :headline1";
    if ($forExternal)
      $sql.= " WHERE allow_external=1";
    $sql.= " OR tbl_solvers.experience LIKE :headline2
      LIMIT $take OFFSET $skip;";

    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':headline1', "%" . $keyword . "%");
    $stmt->bindValue(':headline2', "%" . $keyword . "%");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function updateSolverDetails($solverId, $data, $files) {
    $publicId = $solverId;
    $solverId = $this->getRealId($solverId);
    if ($solverId) {
      $success = [];
      $failure = [];
  
      if (isset($data) && isset($data['abstract'])) {
        if (strlen($data['abstract']) >= 255) {
          array_push($failure, "abstract (too long)");
        } else {
          $sql = "UPDATE tbl_solvers SET abstract = :abstract";
          $sql .= " WHERE id = :solverid";
          $stmt = $this->db->pdo->prepare($sql);
          $stmt->bindValue(':solverid', $solverId);
          $stmt->bindValue(':abstract', $data['abstract']);
          $result = $stmt->execute();
          if ($result)
            array_push($success, "abstract");
          else
            array_push($failure, "abstract");  
        }
      } 
  
      if(isset($_FILES['portraitImage']['tmp_name']) && is_uploaded_file($_FILES['portraitImage']['tmp_name'])){
        $allowedTypes = array(IMAGETYPE_PNG);
        $detectedType = exif_imagetype($_FILES['portraitImage']['tmp_name']);
        if (in_array($detectedType, $allowedTypes)) {
          $bannerImg = file_get_contents($_FILES['portraitImage']['tmp_name']);
          $sql = "UPDATE tbl_solvers set portraitImage=? WHERE id=$solverId";
          $stmt = $this->db->pdo->prepare($sql);
          $stmt->bindParam(1, $bannerImg, PDO::PARAM_LOB);
          $result = $stmt->execute();
          if ($result)
              array_push($success, "portrait image");
            else
              array_push($failure, "portrait image");
        } else {
          array_push($failure, "portrait image (must be image/png, got " . $_FILES['portraitImage']['type'] .")");
        }
      }
  
      if(isset($_FILES['bannerImage']['tmp_name']) && is_uploaded_file($_FILES['bannerImage']['tmp_name'])){
        $allowedTypes = array(IMAGETYPE_PNG);
        $detectedType = exif_imagetype($_FILES['bannerImage']['tmp_name']);
        if (in_array($detectedType, $allowedTypes)) {
          $bannerImg = file_get_contents($_FILES['bannerImage']['tmp_name']);
          $sql = "UPDATE tbl_solvers set bannerImage=? WHERE id=$solverId;";
          $stmt = $this->db->pdo->prepare($sql);
          $stmt->bindParam(1, $bannerImg, PDO::PARAM_LOB);
          $result = $stmt->execute();
          if ($result)
              array_push($success, "banner image");
            else
              array_push($failure, "banner image");
        } else {
          array_push($failure, "banner image (must be image/png, got " . $_FILES['bannerImage']['type'] . ")");
        }
      }
  
      if (isset($data['skillsIds']) && $data['skillsIds'] != "") {
        $skillIds = $data['skillsIds'];
        $skillIds = rtrim($skillIds, ',');
        $result = $this->updateSkillsForSolver($publicId, $skillIds);
        if ($result)
          array_push($success, "skills");
        else
          array_push($failure, "skills");
      }
      $message = "";
      if (count($success) > 0) {
        $message .= "Saved ";
        foreach ($success as $s) {
          $message .= $s . ", ";
        }
        $message = substr($message, 0, strlen($message)-2) . ". ";
      }
      if (count($failure) > 0) {
        $message .= "Failed to update ";
        foreach ($failure as $f) {
          $message .= $f . ", " ;
        }
        $message = substr($message, 0, strlen($message)-2) . ".";
      }
      if (count($failure) > 0) {
        return createUserMessage("error", $message);
      } else {
        Session::set('pendingMsg', createUserMessage("success", $message));
        $onboarding=Session::get('onboarding');
        if (isset($onboarding) && isset($onboarding->completedUrl))
          return "<script>location.href='" . $onboarding->completedUrl . "&solverid=" . $solverId . "';</script>";
        else
          return "<script>location.href='solverView.php?solverid=" . $publicId . "';</script>";
      }
    }
  }

  public function updateSolverById($solverId, $data, $organizations, $users){
    $publicId = $solverId;
    $solverId = $this->getRealId($solverId);
    if ($solverId) {
      if (!$organizations->checkOrganizationAdmin($data['fk_org_id'], Session::get("userid"), $users) && Session::get("roleid") != '1') {
        Session::set('pendingMsg', createUserMessage("error", "You do not have permission to change this solver!"));
        return "<script>location.href='solverView.php?solverid=" . $solverId . "';</script>";
      }

      if ($data['headline'] == "" || $data['experience'] == "" || $data['availability'] == "" || $data['rate'] == "" || $data['locations'] == "") {
        return createUserMessage("error", "All fields are required!");
      } elseif (containsBadWords($data['headline'])) {
        return createUserMessage("error", "Headline should not contain foul language.");
      } elseif (strlen($data['headline']) < 14) {
        return createUserMessage("error", "Solver headline must be at least 14 characters.");
      } elseif(strlen($data['experience']) < 50) {
        return createUserMessage("error", "Please provide a detailed description (at least 50 characters) of your experience.");
      } else {
        $sql = "UPDATE tbl_solvers SET
          headline = :headline,
          fk_org_id = :orgid,
          experience = :experience,
          availability = :availability,
          rate = :rate,
          locations = :locations";
        //Allow admin to flag as coach
        if (array_key_exists('was_coach', $data) && ($data['is_coach'] != $data['was_coach']) && Session::get("roleid") == '1') {
          if (!isset($data['is_coach']))
            $coachVal = "0";
          else
            $coachVal = "1";
          $sql .= ", is_coach = " . $coachVal;
        }
        //Allow admin to flag for external marketplaces
        if (array_key_exists('was_external', $data) && ($data['allow_external'] != $data['was_external']) && Session::get("roleid") == '1') {
          if (!isset($data['allow_external']))
            $externalVal = "0";
          else
            $externalVal = "1";
          $sql .= ", allow_external = " . $externalVal;
        }
        $sql .= " WHERE id = :solverid";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':headline', $data['headline']);
        $stmt->bindValue(':orgid', $data['fk_org_id']);
        $stmt->bindValue(':experience', $data['experience']);
        $stmt->bindValue(':availability', $data['availability']);
        $stmt->bindValue(':rate', $data['rate']);
        $stmt->bindValue(':locations', $data['locations']);
        $stmt->bindValue(':solverid', $solverId);
        $result = $stmt->execute();
        if ($result) {
          Session::set('pendingMsg', createUserMessage("success", "Solver profile updated."));
          return "<script>location.href='solverDetail.php?solverid=" . $publicId . "';</script>";
        } else {
          return createUserMessage("error", "Solver profile could not be updated. Something went wrong!");
        }
      }
    }
  }

  public function updateSkillsForSolver($solverId, $skillIds) {
    $solverId = $this->getRealId($solverId);
    if ($solverId) {
      //Clear out previous skills list
      $sql = "DELETE FROM tbl_solver_skills WHERE fk_solver_id=:solverid";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':solverid', $solverId);
      $stmt->execute();

      //Now insert skills
      $result = null;
      $skillIds = explode(",", $skillIds);    
      foreach ($skillIds as $skillId) {
        if (is_numeric($solverId) && is_numeric($skillId)) {
          $sql = "INSERT INTO tbl_solver_skills (fk_solver_id, fk_skill_id) VALUES (:solverid, :skillid)";
          $stmt = $this->db->pdo->prepare($sql);
          $stmt->bindValue(':solverid', $solverId);
          $stmt->bindValue(':skillid', $skillId);
          $result = $stmt->execute();  
        }
      }
      return $result;
    }
    return false;
  }

  public function deleteSolverProfile($solverId){
    $solverId = $this->getRealId($solverId);
    if ($solverId) {
      $sql = "DELETE FROM tbl_solvers WHERE id = :id;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':id', $solverId);
      $result = $stmt->execute();
      if ($result) {
        return createUserMessage("success", "Solver Profile deleted.");
      } else {
        return createUserMessage("error", "Solver Profile not deleted. Something went wrong!");
      }
    }
  }
}
