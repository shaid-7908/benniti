<?php
include_once $docRoot."lib/Database.php";
include_once $docRoot."lib/Session.php";

/* Organizations operate on public_id */
$snowflake = new \Godruoyi\Snowflake\Snowflake;

class Opportunities {

  private $db;

  public function __construct() {
    $this->db = new Database();
  }

  public function getRealId($publicId) {
    if (!isset($publicId) || $publicId == "") {
      error_log("Could not find real id when public_id not set");
      return false;
    }
    $sql = "SELECT id FROM tbl_opportunities WHERE public_id = :publicid";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':publicid', $publicId);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    if ($result) {
      return $result[0]->id;
    } else {
      error_log("Could not find real id for opportunities with public id " . $publicId);
      return false;
    }
  }

  public function checkOpportunityExistsByHeadlineAndRealOrgId($opportunityHeadline, $orgId, $organizations) {
    if ($orgId) {
      $sql = "SELECT headline from tbl_opportunities WHERE LOWER(headline) = LOWER(:headline) AND fk_org_id = :orgid";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':headline', $opportunityHeadline);
      $stmt->bindValue(':orgid', $orgId);
      $stmt->execute();
      if ($stmt->rowCount()> 0) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function checkOpportunityExistsById($opportunityId) {
    $opportunityId = $this->getRealId($opportunityId);
    if ($opportunityId) {
      $sql = "SELECT headline from tbl_opportunities WHERE id = :opportunityId";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':opportunityId', $opportunityId);
      $stmt->execute();
      if ($stmt->rowCount()> 0) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function checkOpportunityAdmin($opportunityId, $orgId, $organizations) {
    $opportunityId = $this->getRealId($opportunityId);
    $orgId = $organizations->getRealId($orgId);
    if ($opportunityId && $orgId) {
      $sql = "SELECT creator, id from tbl_opportunities WHERE id = :opportunityId AND creator = :userId LIMIT 1";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':opportunityId', $opportunityId);
      $stmt->execute();
      if ($stmt->rowCount()> 0) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function createOpportunity($data, $byAdmin, $organizations, $users) {
    $skillIds = $data['skillsIds'];
    $skillIds = rtrim($skillIds, ',');
    $userid = $users->getRealId(Session::get("userid"));
    $orgId = $data['fk_org_id'];

    $snowflake = new \Godruoyi\Snowflake\Snowflake;
    $newPublicId = $snowflake->id();

    if ($data['headline'] == "" || $data['fk_org_id'] == "" || $data['requirements'] == "" || $data['date'] == "" || $data['rate'] == "" || $data['location'] == "") {
      return createUserMessage("error", "All fields are required!");
    } elseif (containsBadWords($data['headline'])) {
      return createUserMessage("error", "Opportunity should not contain foul language.");
    } elseif (strlen($data['headline']) < 14) {
      return createUserMessage("error", "Opportunity headline must be at least 14 characters.");
    } elseif(strlen($data['requirements']) < 100) {
      return createUserMessage("error", "Please provide a detailed description (at least 100 characters) of your opportunity.");
    } elseif(strlen($data['location']) < 5) {
      return createUserMessage("error", "Please provide more information about your location (at least 5 characters)");
    } elseif ($this->checkOpportunityExistsByHeadlineAndRealOrgId($data['headline'], $data['fk_org_id'], $organizations) == TRUE) {
      return createUserMessage("error", "An opportunity with that headline already exists in your organization. You can modify that opportunity, or create a new one with a different name.");
    } else {
      $sql = "INSERT INTO tbl_opportunities(public_id, fk_user_id, fk_org_id, headline, requirements, start_date, complete_date, rate,rate_type, location,address_line_1,address_line_2,city,state,zip_code) 
        VALUES (:publicid, :userid, :orgid, :headline, :requirements, :start_date, :complete_date, :rate,:rate_type, :location,:address_line_1,:address_line_2,:city,:state,:zip_code)";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':publicid', $newPublicId);
      $stmt->bindValue(':userid', $userid);
      $stmt->bindValue(':orgid', $orgId);
      $stmt->bindValue(':headline', $data['headline']);
      $stmt->bindValue(':requirements', $data['requirements']);
      if($data['date'] == 'yes'){

        $stmt->bindValue(':start_date', $data['start_date']);
        $stmt->bindValue(':complete_date', $data['complete_date']);
      }else{
        $stmt->bindValue(':start_date',$data['date']);
        $stmt->bindValue(':complete_date', 'null');
      }
      if($data['rate'] == 'yes'){
        $stmt->bindValue(':rate',$data['rate-value']);
        $stmt->bindValue(':rate_type',$data['rate_type']);
      }else{

        $stmt->bindValue(':rate', $data['rate']);
         $stmt->bindValue(':rate_type','null');
      }
      if($data['location'] == 'On premise' || $data['location'] == 'hybrid'){

        $stmt->bindValue(':location', $data['location']);
        $stmt->bindValue(':address_line_1',$data['address1']);
        $stmt->bindValue(':address_line_2',$data['address2']);
        $stmt->bindValue(':city',$data['city']);
        $stmt->bindValue(':state',$data['state']);
        $stmt->bindValue(':zip_code',$data['zip']);
      }else{
           $stmt->bindValue(':location', $data['location']);
        $stmt->bindValue(':address_line_1','na');
        $stmt->bindValue(':address_line_2','na');
        $stmt->bindValue(':city','na');
        $stmt->bindValue(':state','na');
        $stmt->bindValue(':zip_code','na');
      }
      $result = $stmt->execute();
      if ($result) {
        $newOptyId = $this->db->pdo->lastInsertId();
        $result = $this->updateSkillsForOpportunity($newPublicId, $skillIds);
        if ($result) {
          Session::set('pendingMsg', createUserMessage("success", "Opportunity created."));
          return "<script>location.href='opportunitySuccess.php';</script>";
        } else {
          return createUserMessage("error", "Opportunity skills not created. Something went wrong!");
        }
      } else {
        return createUserMessage("error", "Opportunity not created. Something went wrong!");
      }
    }
  }

  public function getAllOpportunityData($orgId, $organizations) {
    if (!isset($orgId) && Session::get('roleid') != 1) {
      return null;
    }
    if (isset($orgId) && $orgId != null)
      $orgId = $organizations->getRealId($orgId);
    $sql = "SELECT tbl_opportunities.*, 
      tbl_organizations.orgname as orgname
      FROM tbl_opportunities
      INNER JOIN tbl_organizations ON tbl_opportunities.fk_org_id=tbl_organizations.id";
    if (isset($orgId) && $orgId != null) {
        $sql = $sql . " WHERE tbl_opportunities.fk_org_id=:orgid";
    }
    $sql = $sql . " ORDER BY tbl_opportunities.id DESC;";
    $stmt = $this->db->pdo->prepare($sql);
    if (isset($orgId))
      $stmt->bindValue(':orgid', $orgId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function getAllOpportunityDataForUser($userId, $users) {
    $userId = $users->getRealId($userId);
    if ($userId) {
      $sql = "SELECT tbl_opportunities.*, 
        tbl_organizations.orgname as orgname
        FROM tbl_opportunities
        INNER JOIN tbl_organizations ON tbl_opportunities.fk_org_id=tbl_organizations.id 
        WHERE tbl_opportunities.fk_user_id = :userid ";
      $sql = $sql . " ORDER BY tbl_opportunities.id DESC;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':userid', $userId);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_OBJ);
    } else {
      return false;
    }
  }



  public function getAllOpportunityDataForOrg($orgId, $organizations) {
    $orgId = $organizations->getRealId($orgId);
    if ($orgId) {
      $sql = "SELECT tbl_opportunities.*, 
        tbl_organizations.orgname as orgname
        FROM tbl_opportunities
        INNER JOIN tbl_organizations ON tbl_opportunities.fk_org_id=tbl_organizations.id 
        WHERE tbl_opportunities.fk_org_id = :orgid ";
      $sql = $sql . " ORDER BY tbl_opportunities.id DESC;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':orgid', $orgId);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_OBJ);
    } else {
      return false;
    }
  }

public function searchOpportunities($searchString,$userid) {
    $searchStrings = explode(" ", $searchString);
    $results = [];
    
    foreach ($searchStrings as $str) {
        $keywords = $this->searchOpportunityByKeyword($str,$userid);
        foreach ($keywords as $keyword) {
            // Check if an object with the same ID already exists
            if (!$this->isObjectWithSameIdPresent($results, $keyword->public_id)) {
                array_push($results, $keyword);
            }
        }
        
        $skillResults = $this->searchOpportunityBySkill($str,$userid);
        foreach ($skillResults as $skillResult) {
            // Check if an object with the same ID already exists
            if (!$this->isObjectWithSameIdPresent($results, $skillResult->public_id)) {
                array_push($results, $skillResult);
            }
        }
    }
    
    return $results;
}

// Function to check if an object with the same ID exists in the array
private function isObjectWithSameIdPresent($array, $id) {
    foreach ($array as $item) {
        if ($item->public_id === $id) {
            return true;
        }
    }
    return false;
}


  public function searchOpportunityByKeyword($keyword,$userid) {
    $sql = "SELECT tbl_opportunities.*, 
      tbl_organizations.orgname as orgname 
      FROM tbl_opportunities 
      INNER JOIN tbl_organizations ON tbl_opportunities.fk_org_id=tbl_organizations.id 
      WHERE tbl_opportunities.fk_user_id != :userid AND ( tbl_opportunities.headline LIKE :headline1
      OR tbl_opportunities.requirements LIKE :headline2);";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':headline1', '%' . $keyword . '%');
    $stmt->bindValue(':headline2', '%' . $keyword . '%');
    $stmt->bindValue(':userid',$userid);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

public function searchOpportunityBySkill($skillName,$userid) {
    $sql = "SELECT tbl_opportunity_skills.*,tbl_opportunities.*,tbl_skills.*
            FROM tbl_opportunity_skills
            INNER JOIN tbl_opportunities ON tbl_opportunity_skills.fk_opportunity_id =tbl_opportunities.id
            INNER JOIN tbl_skills ON tbl_opportunity_skills.fk_skill_id = tbl_skills.id
            WHERE tbl_skills.skill_name LIKE :skillName AND tbl_opportunities.fk_user_id != :userid";
    
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':skillName', '%' . $skillName . '%');
    $stmt->bindValue(':userid',$userid);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

//Get lates opportunity
public function findLatestOpportunities($userid) {
    $sql = "SELECT * FROM tbl_opportunities WHERE fk_user_id <> :userid ORDER BY created_at DESC LIMIT 8";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':userid',$userid);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function findLatestOpportunitiesinIndex($userid) {
    $sql = "SELECT * FROM tbl_opportunities WHERE fk_user_id = :userid ORDER BY created_at DESC LIMIT 8";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':userid',$userid);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


  // Get Single Opty Information By Id
  public function getOpportunityInfoById($opportunityId){
    $opportunityId = $this->getRealId($opportunityId);
    if ($opportunityId) {
      $sql = "SELECT tbl_opportunities.*, 
        tbl_organizations.orgname as orgname ,tbl_organizations.public_id as org_public_id
        FROM tbl_opportunities 
        INNER JOIN tbl_organizations ON tbl_opportunities.fk_org_id=tbl_organizations.id 
        WHERE tbl_opportunities.id = :id
        LIMIT 1;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':id', $opportunityId);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_OBJ);
      if ($result) {
        return $result;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function getOpportunityInfoByRealId($opportunityId){
      if ($opportunityId) {
      $sql = "SELECT tbl_opportunities.*, 
        tbl_organizations.orgname as orgname 
        FROM tbl_opportunities 
        INNER JOIN tbl_organizations ON tbl_opportunities.fk_org_id=tbl_organizations.id 
        WHERE tbl_opportunities.id = :id
        LIMIT 1;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':id', $opportunityId);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_OBJ);
      if ($result) {
        return $result;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function updateOpportunityById($opportunityId, $data){
    $userid = Session::get("userid");
    $skillIds = $data['skillsIds'];
    $skillIds = rtrim($skillIds, ',');

    if ($data['headline'] == "" || $data['fk_org_id'] == "" || $data['requirements'] == "" || $data['start_date'] == "" || $data['location'] == "") {
      return createUserMessage("error", "All fields are required!");
    } elseif (containsBadWords($data['headline'])) {
      return createUserMessage("error", "Opportunity should not contain foul language.");
    } elseif (strlen($data['headline']) < 14) {
      return createUserMessage("error", "Opportunity headline must be at least 14 characters.");
    } elseif(strlen($data['requirements']) < 100) {
      return createUserMessage("error", "Please provide a detailed description (at least 100 characters) of your opportunity.");
    } elseif(strtolower($data['location']) != "remote" && !preg_match("#[0-9]+#", $data['location'])) {
      return createUserMessage("error", "Please provide a full address for location.");
    } elseif (strlen($data['start_date']) < 3) {
      return createUserMessage("error", "Please provide a valid date for Start Date");
    } else {

      $sql = "UPDATE tbl_opportunities SET
        fk_user_id = :userid,
        fk_org_id = :orgid,
        headline = :headline,
        requirements = :requirements,
        start_date = :start_date, 
        complete_date = :complete_date, 
        rate = :rate, 
        location = :location
        WHERE id = :optyid";
      $stmt = $this->db->pdo->prepare($sql);
      
      $stmt->bindValue(':orgid', $data['fk_org_id']);
      $stmt->bindValue(':headline', $data['headline']);
      $stmt->bindValue(':requirements', $data['requirements']);
      $stmt->bindValue(':start_date', $data['start_date']);
      $stmt->bindValue(':complete_date', $data['complete_date']);
      $stmt->bindValue(':rate', $data['rate']);
      $stmt->bindValue(':location', $data['location']);
      $stmt->bindValue(':optyid', $opportunityId);
      $result = $stmt->execute();
      if ($result) {
        $result = $this->updateSkillsForOpportunity($opportunityId, $skillIds);
        if ($result) {
          Session::set('pendingMsg', createUserMessage("success", "Opportunity updated."));
          return "<script>location.href='opportunityList.php';</script>";
        } else {
          return createUserMessage("error", "Opportunity skills could not be updated. Something went wrong!");
        }
      } else {
        return createUserMessage("error", "Opportunity could not be updated. Something went wrong!");
      }
    }
  }

  public function updateModalOpportunityById( $data){
    $userid = Session::get("userid");
    $skillIds = $data['skillsIds'];
    $skillIds = rtrim($skillIds, ',');

    if ($data['headline'] == "" || $data['fk_org_id'] == "" || $data['requirements'] == "" || $data['date'] == "" || $data['location'] == "") {
      return createUserMessage("error", "All fields are required!");
    } elseif (containsBadWords($data['headline'])) {
      return createUserMessage("error", "Opportunity should not contain foul language.");
    } elseif (strlen($data['headline']) < 14) {
      return createUserMessage("error", "Opportunity headline must be at least 14 characters.");
    } elseif(strlen($data['requirements']) < 100) {
      return createUserMessage("error", "Please provide a detailed description (at least 100 characters) of your opportunity.");
    } elseif(strtolower($data['location']) <5) {
      return createUserMessage("error", "Please provide a full address for location.");
    } elseif (strlen($data['date']) < 2) {
      return createUserMessage("error", "Please provide a valid date for Start Date");
    } else {

      $sql = "UPDATE tbl_opportunities SET
                
                headline = :headline,
                requirements = :requirements,
                start_date = :start_date, 
                complete_date = :complete_date, 
                rate = :rate, 
                location = :location,
                address_line_1 = :address_line_1,
                address_line_2 = :address_line_2,
                city = :city,
                state = :state,
                zip_code = :zip_code
                WHERE id = :optyid";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':headline', $data['headline']);
      $stmt->bindValue(':requirements', $data['requirements']);
      if($data['date'] == 'yes'){

        $stmt->bindValue(':start_date', $data['start_date']);
      }else{
        $stmt->bindValue(':start_date',$data['date']);
      }
      $stmt->bindValue(':complete_date', 'null');
      if($data['rate'] == 'yes'){

        $stmt->bindValue(':rate', $data['rate_value']);
      }else{
        $stmt->bindValue(':rate',$data['rate']);
      }
      if($data['location'] == 'on_premise' || $data['location'] == 'hybrid'){

        $stmt->bindValue(':location', $data['location']);
        $stmt->bindValue(':address_line_1',$data['address1']);
        $stmt->bindValue(':address_line_2',$data['address2']);
        $stmt->bindValue(':city',$data['city']);
        $stmt->bindValue(':state',$data['state']);
        $stmt->bindValue(':zip_code',$data['zip']);
      }else{
           $stmt->bindValue(':location', $data['location']);
        $stmt->bindValue(':address_line_1','na');
        $stmt->bindValue(':address_line_2','na');
        $stmt->bindValue(':city','na');
        $stmt->bindValue(':state','na');
        $stmt->bindValue(':zip_code','na');
      }
      $stmt->bindValue(':optyid', $data['optyid']);
      $result = $stmt->execute();
      if ($result) {
        $result = $this->updateModalSkillsForOpportunity($data['optyid'], $skillIds);
        if ($result) {
          Session::set('pendingMsg', createUserMessage("success", "Opportunity updated."));
          return "<script>location.href='opportunityList.php';</script>";
        } else {
          return createUserMessage("error", "Opportunity skills could not be updated. Something went wrong!");
        }
      } else {
        return createUserMessage("error", "Opportunity could not be updated. Something went wrong!");
      }
    }
  }

  public function updateSkillsForOpportunity($opportunityId, $skillIds) {
    $opportunityId = $this->getRealId($opportunityId);
    if ($opportunityId) {
      //Clear out previous skills list
      $sql = "DELETE FROM tbl_opportunity_skills WHERE fk_opportunity_id=:opptyid";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':opptyid', $opportunityId);
      $stmt->execute();

      //Now insert skills
      $skillIds = explode(",", $skillIds);        
      foreach ($skillIds as $skillId) {
        $sql = "INSERT INTO tbl_opportunity_skills (fk_opportunity_id, fk_skill_id) VALUES (:optyid, :skillid)";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':optyid', $opportunityId);
        $stmt->bindValue(':skillid', $skillId);
        $result = $stmt->execute();
      }
      return $result;
    } else {
      return false;
    }
  }
  public function updateModalSkillsForOpportunity($opportunityId, $skillIds) {
    if ($opportunityId) {
      //Clear out previous skills list
      $sql = "DELETE FROM tbl_opportunity_skills WHERE fk_opportunity_id=:opptyid";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':opptyid', $opportunityId);
      $stmt->execute();

      //Now insert skills
      $skillIds = explode(",", $skillIds);        
      foreach ($skillIds as $skillId) {
        $sql = "INSERT INTO tbl_opportunity_skills (fk_opportunity_id, fk_skill_id) VALUES (:optyid, :skillid)";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':optyid', $opportunityId);
        $stmt->bindValue(':skillid', $skillId);
        $result = $stmt->execute();
      }
      return $result;
    } else {
      return false;
    }
  }

  public function deleteOpportunityById($opportunityId){
    $opportunityId = $this->getRealId($opportunityId);
    $sql = "DELETE FROM tbl_opportunities WHERE id = :id;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':id', $opportunityId);
    $result = $stmt->execute();
    if ($result) {
      $sql = "DELETE FROM tbl_opportunity_skills WHERE fk_opportunity_id = :id;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':id', $opportunityId);
      $result = $stmt->execute();
      if ($result) {
        Session::set('pendingMsg', createUserMessage("success", "Opportunity deleted."));
        return "<script>location.href='opportunityList.php';</script>"; 
      } else {
        return createUserMessage("error", "Opportunity skills not deleted. Something went wrong!");
      }
    } else {
      return createUserMessage("error", "Opportunity not deleted. Something went wrong!");
    }
  }
}
