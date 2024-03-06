<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/header.php';
include 'inc/topbar.php';
Session::checkSession();
$views->showAndClearPendingMessage();

//Check if user is ready to view this page
if (Session::get("roleid") > 2) {
  $userOrgs = $organizations->checkUserHasOrganizationOrRedirect($users);
  $foundSolver = false;
  if (isset($userOrgs) && is_array($userOrgs) && count($userOrgs) > 0) {
    foreach ($userOrgs as $thisOrg) {
      if ($solvers->getSolverProfileByOrgIdAndUserId($thisOrg->public_id, Session::get("userid"), $organizations, $users)) {
        $foundSolver = true;
        break;
      }
    }
  }
  if (!$foundSolver && isset($_GET['query'])) {
    header('Location: solverRequired.php');
    die();
  }
}
$user_real_id = $users->getRealId(Session::get("userid"));

//Figure out what we're working on
$theQuery = "";
$skills1 = ""; //TODO: Support searching by skills
if (isset($_GET["query"])) {
  $theQuery = $_GET["query"];
}
if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST["query"])) {
  $theQuery = $_POST["query"];
}

//Check permissions and process requested actions
if (isset($_GET["action"]) && $_GET["opportunityid"] && is_numeric($_GET["opportunityid"])) {
  $allowed = false;
  $removeId = (int)$_GET["opportunityid"];
  if (checkUserAuth($_GET["action"] . "_orthogonal", Session::get('roleid'))) {
    //Admins are allowed to manage opportunities
    $allowed = true;
  } else {
    //Check if this opportunity belongs to this user
    $userOpty = $opportunities->getOpportunityInfoById($removeId);
    if (isset($userOpty) && checkUserAuth("edit_opportunity", Session::get('roleid'))) {
      if (getIfSet($userOpty, "fk_user_id") ==  $users->getRealId(Session::get("userid"))) {
        $allowed = true;
      }
    }
  }
  if (!$allowed && isset($userOpty)) {
    //Org admins are allowed to manage opportunities in their org
    $orgLevel = $organizations->getUserOrganizationLevel(getIfSet($userOpty, "fk_org_id"), Session::get("userid"), $users);
    if (checkUserAuth($_GET["action"], $orgLevel)) {
      $allowed = true;
    }
  }
  if (!$allowed) {
    //Warn about disallowed action
    error_log("Insufficient privileges, user " . Session::get("userid") . " with level " . Session::get('roleid') . " attempted to delete an opportunity they did not own, and was ejected.");
    Session::set('pendingMsg', createUserMessage("error", "You may not manage opportunities you do not own!"));
    header('Location:opportunityList.php');
  } else {
    //Actually do the delete
    $removeOpty = $opportunities->deleteOpportunityById($removeId);
    if (isset($removeOpty)) {
      echo $removeOpty;
    }
  }
}

if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['updateOpty'])) {
  $getOInfo = $_POST;
  print_r($_POST);
  $updateOpty = $opportunities->updateModalOpportunityById($_POST);
  //Show pending results
  if (isset($updateOpty)) {
    echo $updateOpty;
  }
}
?>
<!-- The Modal -->
<script src="scripts/skillsAjax.js"></script>
<form name="frmOpportunityEdit" id="frmOpportunityEdit" class="" action="" method="POST">
  <div class="modal modal_outer right_modal fade" style="color: black;" id="editmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog " role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Edit Opportunity</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">



















          < </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button id="btnControlSubmit" class="bt btn-succes" style="background-color: #F5A800; padding: 4px 8px 4px 8px;color: black;border: none ; border-radius: 4px; width:130px;height: 40px;font-weight: 600;" onclick="controlledFormSubmit('frmOpportunityEdit')">Submit</button>

            </div>
        </div>

      </div>


    </div>
</form>
<div class="xt-card-organization" style="color:black;position: relative;">

  <?php
  //Show search form if needed
  if (strpos($_SERVER["QUERY_STRING"], "query") !== false) {
  ?>
    <div class="xt-sidebar-organization ">
      
    <?php include 'inc/sidebar.php' ?>
       <div style="border-top: 2px solid #053B45;padding: 8px;">
      <a href="https://www.bennit.ai/" target="_blank">
        <span style="text-decoration: underline; color:#F5A800;font-size: 14px;">
          Bennit.Ai
        </span>
      </a>
    </div>
    </div>
    <div class="xt-body-organization" style="background-color: green;">
      <div style="width:600px; margin:0px auto">
        <form class="" action="" method="GET">
          <div class="form-group">
            <label for="query"><b>Search String</b> - Key words to search for</label>
            <input type="text" name="query" id="query" value="<?php if (isset($theQuery)) {
                                                                echo $theQuery;
                                                              } ?>" class="form-control" minlength="3" required>
          </div>
          <div class="form-group">
            <label for="skills"><b>Skills</b> - Skills to search for</label>
            <input type="text" id="skills" name="skills" value="" class="form-control" disabled="true">
          </div>

          <div class="form-group">
            <button type="submit" name="dosearch" class="btn btn-default">Search</button>
          </div>
        </form>
      </div>
    </div>
  <?php
  } else {
  ?>
    <div class="xt-sidebar-organization ">
      <?php include 'inc/sidebar.php' ?>
       <div style="border-top: 2px solid #053B45;padding: 8px;">
      <a href="https://www.bennit.ai/" target="_blank">
        <span style="text-decoration: underline; color:#F5A800;font-size: 14px;">
          Bennit.Ai
        </span>
      </a>
    </div>
    </div>
  <?php
  }
  //Determine settings and request grid
  $gridColumns = ["location"];
  //If there's a query to load
  if ($theQuery != "") {
    $allOpty = $opportunities->searchOpportunities($theQuery, $user_real_id);
    if (Session::get("roleid") == '1') {
      $gridActions = ["view", "edit", "delete"];
      array_push($gridColumns, "organization");
      array_push($gridColumns, "location");
    } else {
      $gridActions = ["view", "match"];
    }
  }
  //If we're loaded without a query
  if ($theQuery == "" && strpos($_SERVER["QUERY_STRING"], "query") === false) {
    array_push($gridColumns, "organization");
    array_push($gridColumns, "location");
    //If we're loaded for admin
    if (Session::get("roleid") == '1' && isset($_GET["as"]) && $_GET["as"] == "admin") {
      $allOpty = $opportunities->getAllOpportunityData(null, $organizations);
      $gridActions = ["view", "edit", "delete", "adminmatch"];
    } else {  //Default load
      if (isset($_GET["org"]) && is_numeric($_GET["org"]))
        $allOpty = $opportunities->getAllOpportunityData($_GET["org"], $organizations);
      else
        $allOpty = $opportunities->getAllOpportunityDataForUser(Session::get("userid"), $users);


      $gridActions = ["view", "edit", "delete"];
    }
  }
  ?>
  <?php
  // style here
  ?>
  <div class="xt-body-organization  pr-2 pl-2 " style="padding:20px !important;">
    <?php
    if (strpos($_SERVER["QUERY_STRING"], "query") === false) {
      echo '<div style="display: flex; margin-bottom: 5px; margin-top: 5px; justify-content: space-between; align-items: center;">';
      // Display opportunity count container
      echo '<div class="opportunity-count-container">';
      echo '<p style="width: 265px; height: 32px; top: 4px;
    font-size: 24px;
    font-weight: 700;
    line-height: 32px;
    letter-spacing: 0em;
    text-align: left;
    color: #000000DE;
    ">My Opportunities(' . count($allOpty) . ')</p>';
      echo '</div>';

      echo '<a class="nav-link" href="opportunity.php?action=create_opportunity"
    style="border-radius: 4px; background-color: #F5A800; color: #000000;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 0px;
    text-align: left;
    margin-bottom:8px;
    ">Create New Opportunity</a>';
      echo '</div>';
    }

    if (isset($allOpty)) {
      $views->makeOpportunityGrid($allOpty, $gridColumns, $gridActions);
    }
    ?>

  </div>
  <!-- The Modal -->


</div>
<!-- <script>
  // Get the modal
  var modal = document.getElementById("myModal");

  // Get the button that opens the modal
  var btn = document.getElementById("editBtn");

  // Get the <span> element that closes the modal
  var span = document.getElementsByClassName("close")[0];

  // Get the content element in the modal
  var modalContent = document.getElementById("modalContent");

  // When the user clicks the button, open the modal 
  btn.onclick = function() {
    // Get the public_id from the button's data attribute
    var publicId = this.getAttribute("data-public-id");

    // Populate modal content with dynamic data
    modalContent.innerHTML = "Dynamic content with public ID: " + publicId; // Replace with your dynamic content
    modalContent.style.color = "black";
    modal.style.display = "block";
    modal.style.color = "black";
  }

  // When the user clicks on <span> (x), close the modal
  span.onclick = function() {
    modal.style.display = "none";
  }

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }
</script> -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
  const form = document.getElementById('frmOpportunity')
  const addressField = document.querySelector('.address-field');
  const payRate = document.querySelector('.pay-rate')
  const rangeDate = document.querySelector('.range-date')

  form.addEventListener('change', function(event) {
    if (event.target && event.target.name === 'location') {
      if (event.target.value === 'remote') {
        addressField.style.display = 'none';
      } else {
        addressField.style.display = 'block';
      }
    }
  });

  form.addEventListener('change', function(event) {
    if (event.target && event.target.name === 'rate') {
      if (event.target.value === 'no') {
        payRate.style.display = 'none';
      } else {
        payRate.style.display = 'block'
      }
    }
  });

  form.addEventListener('change', function(event) {
    if (event.target && event.target.name === 'date') {
      if (event.target.value === 'no') {
        rangeDate.style.display = 'none';
      } else {
        rangeDate.style.display = 'block'
      }
    }
  });


  // functions to add skill as div
  function addSkill(event) {
    if (event.key === 'Enter') {
      event.preventDefault();

      // Get input value
      const skill = document.getElementById('skillsText').value.trim();
      if (skill !== '') {
        // Add skill to the list
        addSkillToList(skill);
        // Clear input field
        document.getElementById('skillsText').value = '';
      }
    }
  }

  function addSkillToList(skill) {
    // Get the hidden input field
    const skillsIdsInput = document.getElementById('skillsIds');
    // Get the existing skill IDs
    let skillIds = skillsIdsInput.value.split(',');
    // Add new skill ID
    skillIds.push(skill);
    // Update the hidden input field value
    skillsIdsInput.value = skillIds.join(',');

    // Get the skills list container
    const skillsList = document.getElementById('skills-list');
    // Create a new div element for the skill
    const skillDiv = document.createElement('div');
    skillDiv.classList.add('skill');
    skillDiv.addEventListener('click', function() {
      skillDiv.remove();
    });
    // Set the skill text
    // Create a <p> element
    const pTag = document.createElement('div');
    // Set text content to the skill variable
    pTag.textContent = skill;
    pTag.style.marginRight = "4px";
    // Append the <p> element to the skillDiv
    skillDiv.appendChild(pTag);
    skillDiv.style.backgroundColor = '#f0f0f0';
    skillDiv.style.padding = '4px 8px 4px 8px';
    skillDiv.style.margin = '4px';
    skillDiv.style.borderRadius = '4px';
    skillDiv.style.cursor = 'pointer';
    skillDiv.style.maxWidth = 'fit-content'
    skillDiv.style.fontSize = '14px';
    skillDiv.style.fontWeight = '400';
    skillDiv.style.display = 'flex';
    skillDiv.style.alignItems = 'center';
    // Create an SVG element
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    // Set SVG attributes
    svg.setAttribute('width', '16');
    svg.setAttribute('height', '16');
    svg.setAttribute('viewBox', '0 0 16 16');
    // Create SVG path
    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    // Set path attributes
    path.setAttribute('d', 'M6.9987 12.8335C4.0587 12.8335 1.66536 10.4402 1.66536 7.50016C1.66536 4.56016 4.0587 2.16683 6.9987 2.16683C9.9387 2.16683 12.332 4.56016 12.332 7.50016C12.332 10.4402 9.9387 12.8335 6.9987 12.8335ZM6.9987 0.833496C3.31203 0.833496 0.332031 3.8135 0.332031 7.50016C0.332031 11.1868 3.31203 14.1668 6.9987 14.1668C10.6854 14.1668 13.6654 11.1868 13.6654 7.50016C13.6654 3.8135 10.6854 0.833496 6.9987 0.833496ZM8.72536 4.8335L6.9987 6.56016L5.27203 4.8335L4.33203 5.7735L6.0587 7.50016L4.33203 9.22683L5.27203 10.1668L6.9987 8.44016L8.72536 10.1668L9.66536 9.22683L7.9387 7.50016L9.66536 5.7735L8.72536 4.8335Z');
    path.setAttribute('fill', 'black'); // Set fill color to black
    path.setAttribute('fill-opacity', '0.87'); // Set fill opacity to 0.87
    // Append path to SVG
    svg.appendChild(path);
    // Append SVG to skillDiv
    skillDiv.appendChild(svg);
    // Append the skill div to the skills list
    skillsList.appendChild(skillDiv);
  }
</script>
<script>
  $(document).ready(function() {
    $('.editbutton').on('click', function() {

      var opportunityId = $(this).data('id');
      console.log(opportunityId);

      $.ajax({
        url: 'modal_forms/fetch_opportunity.php', // Adjust the URL
        method: 'GET',
        data: {
          id: opportunityId
        },
        success: function(response) {
          $('.modal-body').html(response);
          $('#editmodal').modal('show');
        }
      });


    });
  });
</script>


<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
  flatpickr("#start_date", {
    "mode": "range"

  });
</script>
</body>

</html>