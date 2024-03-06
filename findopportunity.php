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
            $sol = $solvers->getSolverProfileByOrgIdAndUserId($thisOrg->public_id, Session::get("userid"), $organizations, $users);
            if ($solvers->getSolverProfileByOrgIdAndUserId($thisOrg->public_id, Session::get("userid"), $organizations, $users)) {
                $foundSolver = true;
                break;
            }
        }
    }
}

$user_real_id = $users->getRealId(Session::get('userid'));

$solverdata_for_user = $solvers->getMAllSolverDataForUser(Session::get('userid'),$users,$organizations);


//Figure out what we're working on
$theQuery = "";
$skills1 = ""; //TODO: Support searching by skills
if (isset($_GET["query"])) {
    $theQuery = $_GET["query"];
}
if (isset($_GET["skills"])) {
    $skills = $_GET["skills"];
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
if($_SERVER["REQUEST_METHOD"] == 'POST'){
    print_r($_POST);
}
?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<?php
if (!$foundSolver && isset($_GET['query'])) {
    $modalScript = "
        <script>
            $(document).ready(function() {
                $('#exampleModalCenter').modal('show');
            });
        </script>
    ";

    // Echo the JavaScript code to the output
    echo $modalScript;
}


?>
<?php
// crete solver modal for user with no solver profile
?>
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="color: black;">
            <div class="modal-header">
                <div style="color: #012B33;font-size: 20px;font-weight: 700;line-height: 28px;">Create Your Solver Profile </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <div>

                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="black" />
                        </svg>
                    </div>
                </button>
            </div>
            <div class="modal-body">
                <div class="my-2" style="color:black;font-size: 24px;font-weight: 700;line-height: 32px;">Ready to Match?</div>
                <div class="my-2" style="color:black;font-size: 16px;font-weight: 400;line-height: 24px;">Before we can match you with this opportunity, you’ll need to create your Solver profile. This is how we can help determine whether it’s a good match!</div>
            </div>
            <div class="modal-footer" style="display: flex;justify-content: flex-start;">
                <a href="solver.php?action=create_solver" style="color: inherit; text-decoration: none;">
                    <button type="button" style="cursor: pointer; width: 146px;height: 40px; padding: 8px 12px 8px 12px;background-color: #F5A800; color:black;border: 1px solid #F5A800; border-radius: 4px;font-size: 16px;font-weight: 700;line-height: 24px">Create Profile</button>
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php
//match with opportunity modal
?>
<div id="matchWithOpportunity" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
     <div class="modal-content" style=" box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);border-radius: 4px;">
      <div class="modal-header" style="display: flex; align-items: center;">
        <h5 class="modal-title" id="exampleModalLongTitle" style="color: #012B33; font-weight: 700; font-size:20px; line-height: 28px;">Is it a Match ?</h5>
        <button type="button" class="close" data-dismiss="modal" style="display: flex; justify-content: center; margin-right: 2px; align-items: center; height: 40px; width: 40px; border: 2px solid #E7E7E8;" aria-label="Close">
          <div>

            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="black" />
            </svg>
          </div>

        </button>
      </div>


      <form id="solvermatchForm" action="" method="post">
        <div class="modal-body" style="color: black; max-height: 400px; overflow-y: auto;">
          <diV>
            <p style="font-size: 24px;font-weight: 700; list-style: 32px; color:black">Match with <span id="modal-org-name"></span></p>
          </diV>
          <div>
            <p class="inter-font font-16 font-400">Select which Solver you would like to invite <span id="modal-org-name2" style="font-weight: bold;"></span> to help you with.</p>
          </div>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>
                    <input type="checkbox" id="selectAllCheckbox">
                  </th>
                  <th class="inter-font font-700 font-12">Organization name</th>
                  <th class="inter-font font-700 font-12">Experience</th>
                  <th class="inter-font font-700 font-12">Rate</th>
                  
                </tr>
              </thead>
              <tbody>
                <?php foreach ($solverdata_for_user as $solver) : ?>
                  <tr>
                    <td>
                      <input type="checkbox" name="solverCheckbox[]" id="solverCheckbox" class="solverCheckbox" value="<?php echo $solver->public_id ?>">
                    </td>
                    <td class="inter-font font-14 font-500" style=" color:black; text-decoration: underline;"><b><?php echo $solver->orgname ?></b></td>
                    <?php
                       $useHeadline = $solver->headline;
                       $useHeadline = (strlen($useHeadline) > 33) ? substr($useHeadline,0,30).'...' : $useHeadline;
                     ?>
                    <td class="font-14"><?php echo $useHeadline ?></td>
                    <td><?php echo $solver->rate ?></td>
                    
                  </tr>
                <?php endforeach; ?>
              </tbody>

            </table>
          </div>
          <input type="hidden" name="user_public_id" id="user_public_id" value="<?php echo Session :: get('userid') ?>" >
          <div id="opportunity-id-input">

          </div>

          <script>
            document.getElementById('selectAllCheckbox').addEventListener('change', function() {
              var checkboxes = document.getElementsByClassName('solverCheckbox');
              for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = this.checked;
              }
            });
          </script>

        </div>
        <div class="modal-footer" style="display: flex; justify-content: flex-start;">
          <button type="submit" name="submit" id="submit" class="btn " style="padding: 8px 12px 8px 12px; background-color: #F5A800; color:black;font-size: 16px; font-weight: 700;">Submit</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>

      </form>


    </div>
  </div>
</div>

<?php
//opportunity view modal
?>


<div class="xt-card-organization">
    <div class="xt-sidebar-organization">
        <?php include 'inc/sidebar.php' ?>
        <div style="border-top: 2px solid #053B45;padding: 8px;">
            <a href="https://www.bennit.ai/" target="_blank">
                <span style="text-decoration: underline; color:#F5A800;font-size: 14px;">
                    Bennit.Ai
                </span>
            </a>
        </div>
    </div>


    <div class="xt-body-organization" style="color: black;">
        <div class="opportunity-search">
            <div class="xt-body-organization" style="color:black">
                <div style="width:100%; margin:0px auto; border-bottom: 2px solid #E7E7E8;">
                    <form class="" action="" method="GET" id="searchForm" style="display: flex;align-items: center; padding:10px ; justify-content: space-between;">
                        <div style="font-size: 20px;font-weight: 700;">Find Opportunities</div>
                        <div class="" style="width:60%; display: flex; align-items: center; border: 1px solid #E7E7E8; border-radius: 4px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.5 3C11.2239 3 12.8772 3.68482 14.0962 4.90381C15.3152 6.12279 16 7.77609 16 9.5C16 11.11 15.41 12.59 14.44 13.73L14.71 14H15.5L20.5 19L19 20.5L14 15.5V14.71L13.73 14.44C12.5505 15.4468 11.0507 15.9999 9.5 16C7.77609 16 6.12279 15.3152 4.90381 14.0962C3.68482 12.8772 3 11.2239 3 9.5C3 7.77609 3.68482 6.12279 4.90381 4.90381C6.12279 3.68482 7.77609 3 9.5 3ZM9.5 5C7 5 5 7 5 9.5C5 12 7 14 9.5 14C12 14 14 12 14 9.5C14 7 12 5 9.5 5Z" fill="black" fill-opacity="0.38" />
                            </svg>

                            <input type="text" placeholder="Search by keyword or skill, such as “Developer” or “PHP”" name="query" id="query" value="<?php if (isset($theQuery)) {
                                                                                                                                                            echo $theQuery;
                                                                                                                                                        } ?>" style="outline: none; border: none;width:100%;height:2.5rem;background-color: white;color:rgb(46, 46, 46)" minlength="3">
                        </div>
                        <!-- <div class="form-group">
                            <label for="skills"><b>Skills</b> - Skills to search for</label>
                            <input type="text" id="skills" name="skills" value="" class="form-control" >
                        </div> -->


                        <div style="width:134px;text-align: center; font-weight: 600; font-size: 16px; border-radius: 4px;background-color: #F5A800; padding: 8px 12px 8px 12px ; cursor: pointer;" onclick="document.getElementById('searchForm').submit();">
                            Search
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <?php
        // When query is none show this =====================================================================================================================================================================================================================================
        ?>
        <?php
        if ($theQuery == "") {

            $newOpty = $opportunities->findLatestOpportunities($user_real_id);
            if (!empty($newOpty)) {
                // $newOpty has data
        ?> <div style="padding: 10px;color:black">
                    <h3>Latest Opportunities</h3>
                </div>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); grid-gap: 10px;padding: 10px;">
                    <?php foreach ($newOpty as $opportunity) : ?>
                        <div class="card" style="color: black; border-radius: 4px;padding: 4px; border:2px solid #E7E7E8;">
                            <?php // header 
                            ?>
                            <div class="card-body">


                                <div>
                                    <h3 style="font-size: 20px;"><?php echo $opportunity['headline']; ?></h3>
                                </div>
                                <?php // second part 
                                ?>
                                <div class="d-flex align-items-center justify-content-between">
                                    <?php // rate ==== 
                                    ?>
                                    <div class="d-flex align-items-center">
                                        <div><svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M7.99988 3L7.84338 3.14L2.13988 8.9065L1.79688 9.2505L2.14038 9.6105L6.89038 14.3605L7.25038 14.704L7.59538 14.3605L13.3604 8.657L13.4999 8.5V3H7.99988ZM8.42188 4H12.4999V8.078L7.24988 13.297L3.20288 9.25L8.42188 4ZM10.9999 5C10.8673 5 10.7401 5.05268 10.6463 5.14645C10.5526 5.24021 10.4999 5.36739 10.4999 5.5C10.4999 5.63261 10.5526 5.75978 10.6463 5.85355C10.7401 5.94732 10.8673 6 10.9999 6C11.1325 6 11.2597 5.94732 11.3534 5.85355C11.4472 5.75978 11.4999 5.63261 11.4999 5.5C11.4999 5.36739 11.4472 5.24021 11.3534 5.14645C11.2597 5.05268 11.1325 5 10.9999 5Z" fill="black" fill-opacity="0.87" />
                                            </svg>
                                        </div>
                                        <div style="font-size:14px;font-weight:600" class="ml-2">$<?php echo $opportunity['rate']; ?>/hr</div>
                                    </div>
                                    <?php // time 
                                    ?>
                                    <div class="d-flex align-items-center">
                                        <div><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.66667 7.33268H6V8.66602H4.66667V7.33268ZM12.6667 1.99935H12V0.666016H10.6667V1.99935H5.33333V0.666016H4V1.99935H3.33333C2.6 1.99935 2 2.59935 2 3.33268V12.666C2 13.3993 2.6 13.9993 3.33333 13.9993H12.6667C13.4 13.9993 14 13.3993 14 12.666V3.33268C14 2.59935 13.4 1.99935 12.6667 1.99935ZM12.6667 3.33268V4.66602H3.33333V3.33268H12.6667ZM3.33333 12.666V5.99935H12.6667V12.666H3.33333ZM7.33333 9.99935H8.66667V11.3327H7.33333V9.99935ZM10 9.99935H11.3333V11.3327H10V9.99935ZM10 7.33268H11.3333V8.66602H10V7.33268Z" fill="black" fill-opacity="0.87" />
                                            </svg>
                                        </div>
                                        <div class="ml-2 d-flex align-items-center" style="font-size:14px;font-weight:600">
                                            <div><?php echo $opportunity['start_date']; ?></div>
                                        </div>
                                    </div>
                                    <?php ?>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.0013 2.66732C9.46797 2.66732 10.668 3.86732 10.668 5.33398C10.668 6.73398 9.26797 9.00065 8.0013 10.6007C6.73464 8.93398 5.33464 6.73398 5.33464 5.33398C5.33464 3.86732 6.53464 2.66732 8.0013 2.66732ZM8.0013 1.33398C5.8013 1.33398 4.0013 3.13398 4.0013 5.33398C4.0013 8.33398 8.0013 12.6673 8.0013 12.6673C8.0013 12.6673 12.0013 8.26732 12.0013 5.33398C12.0013 3.13398 10.2013 1.33398 8.0013 1.33398ZM8.0013 4.00065C7.26797 4.00065 6.66797 4.60065 6.66797 5.33398C6.66797 6.06732 7.26797 6.66732 8.0013 6.66732C8.73464 6.66732 9.33464 6.06732 9.33464 5.33398C9.33464 4.60065 8.73464 4.00065 8.0013 4.00065ZM13.3346 12.6673C13.3346 14.134 10.9346 15.334 8.0013 15.334C5.06797 15.334 2.66797 14.134 2.66797 12.6673C2.66797 11.8007 3.46797 11.0673 4.73464 10.534L5.13464 11.134C4.46797 11.4673 4.0013 11.8673 4.0013 12.334C4.0013 13.2673 5.8013 14.0007 8.0013 14.0007C10.2013 14.0007 12.0013 13.2673 12.0013 12.334C12.0013 11.8673 11.5346 11.4673 10.8013 11.134L11.2013 10.534C12.5346 11.0673 13.3346 11.8007 13.3346 12.6673Z" fill="black" />
                                            </svg>

                                        </div>
                                        <div class="ml-2 d-flex align-items-center" style="font-size:14px;font-weight:600">
                                            <div><?php echo $opportunity['location']; ?></div>
                                        </div>
                                    </div>
                                </div>
                                <?php // third part 
                                ?>
                                <div class="my-2" style="font-size: 16px; font-weight: 400;">
                                    <?php echo $opportunity['requirements']; ?>
                                </div>

                                <div class="d-flex my-4">
                                    <?php
                                    // Fetch skills for this opportunity
                                    $skills_text = $skills->getAllSkillsForOpportunityById($opportunity['id']);
                                    // Debugging statement to check the value of $skills_text
                                    if (!empty($skills_text)) {
                                        foreach ($skills_text as $skill) {
                                            echo '<div class="mx-2" style="background-color: #E7E7E8; border-radius: 4px; padding:2px 4px 2px 4px;font-size: 14px;font-weight: 400;">' . $skill->skill_name . '</div>';
                                        }
                                    } else {
                                        echo '<div class="mx-2" style="font-size: 14px;font-weight: 400;">No skills found</div>';
                                    }
                                    ?>
                                </div>

                                <div></div>
                            </div>
                            <div class="card-footer d-flex">
                                <a href="viewOpportunity.php?opportunityid=<?php echo $opportunity['public_id'] ?>">
                                <div class=" btn" data-id='<?php echo $opportunity['public_id'] ?>' style="width: 130px;height: 40px;background-color: #F5A800;padding: 8px 12px 8px 12px;font-size: 16px; font-weight: 700;border-radius: 4px;">
                                    View Details
                                </div>
                                </a>
                                    <div class="ml-2 match" data-id='<?php echo $opportunity['public_id'] ?>' style="width: 74px;height: 40px;background-color: #E7E7E8;padding: 8px 12px 8px 12px;font-size: 16px; font-weight: 700;border-radius: 4px;">
                                        Match
                                    </div>
                                


                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php
                // You can then proceed to display the data
            } else {
                // $newOpty is empty (no data)
                echo "No opportunities available.";
                // You may display a message indicating no data is available
            }
        } else {

            ?>


            <?php
            //Determine settings and request grid
            $gridColumns = ["location"];
            //If there's a query to load
            if ($theQuery != "") {
                $allOpty = $opportunities->searchOpportunities($theQuery, $user_real_id);

                $allOpty12 = [];
                foreach ($allOpty as $opty) {
                    if (!in_array($opty, $allOpty12, true)) {
                        $allOpty12[] = $opty;
                    }
                }
                $allOpty = $allOpty12;
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

            <!-- <div class="  pr-2 pl-2" style="color: black;">

                <?php
                if (strpos($_SERVER["QUERY_STRING"], "query") === false)
                    echo '<a class="nav-link" href="opportunity.php?action=create_opportunity"><i class="fas fa-lightbulb mr-2"></i>Create Opportunity</a>';
                if (isset($allOpty))
                    $views->makeOpportunityGrid($allOpty, $gridColumns, $gridActions);
                ?>
            </div> -->
            <?php
            //When user makes a search ====================================================================================================================================================================================
            ?>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); grid-gap: 10px;padding: 10px;">
                <?php foreach ($allOpty as $opportunity) : ?>
                    <div class="card" style="color: black; border-radius: 4px;padding: 4px; border:2px solid #E7E7E8;">
                        <?php // header 
                        ?>
                        <div class="card-body">


                            <div>
                                <h3 style="font-size: 20px;color:#012B33;"><?php echo $opportunity->headline; ?></h3>
                            </div>
                            <?php // second part 
                            ?>
                            <div class="d-flex align-items-center justify-content-between">
                                <?php // rate ==== 
                                ?>
                                <div class="d-flex align-items-center">
                                    <div><svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7.99988 3L7.84338 3.14L2.13988 8.9065L1.79688 9.2505L2.14038 9.6105L6.89038 14.3605L7.25038 14.704L7.59538 14.3605L13.3604 8.657L13.4999 8.5V3H7.99988ZM8.42188 4H12.4999V8.078L7.24988 13.297L3.20288 9.25L8.42188 4ZM10.9999 5C10.8673 5 10.7401 5.05268 10.6463 5.14645C10.5526 5.24021 10.4999 5.36739 10.4999 5.5C10.4999 5.63261 10.5526 5.75978 10.6463 5.85355C10.7401 5.94732 10.8673 6 10.9999 6C11.1325 6 11.2597 5.94732 11.3534 5.85355C11.4472 5.75978 11.4999 5.63261 11.4999 5.5C11.4999 5.36739 11.4472 5.24021 11.3534 5.14645C11.2597 5.05268 11.1325 5 10.9999 5Z" fill="black" fill-opacity="0.87" />
                                        </svg>
                                    </div>
                                    <div style="font-size:14px;font-weight:600;color:#012B33;" class="ml-2">$<?php echo $opportunity->rate; ?>/hr</div>
                                </div>
                                <?php // time 
                                ?>
                                <div class="d-flex align-items-center">
                                    <div><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.66667 7.33268H6V8.66602H4.66667V7.33268ZM12.6667 1.99935H12V0.666016H10.6667V1.99935H5.33333V0.666016H4V1.99935H3.33333C2.6 1.99935 2 2.59935 2 3.33268V12.666C2 13.3993 2.6 13.9993 3.33333 13.9993H12.6667C13.4 13.9993 14 13.3993 14 12.666V3.33268C14 2.59935 13.4 1.99935 12.6667 1.99935ZM12.6667 3.33268V4.66602H3.33333V3.33268H12.6667ZM3.33333 12.666V5.99935H12.6667V12.666H3.33333ZM7.33333 9.99935H8.66667V11.3327H7.33333V9.99935ZM10 9.99935H11.3333V11.3327H10V9.99935ZM10 7.33268H11.3333V8.66602H10V7.33268Z" fill="black" fill-opacity="0.87" />
                                        </svg>
                                    </div>
                                    <div class="ml-2 d-flex align-items-center" style="font-size:14px;font-weight:600;color:#012B33;">
                                        <div><?php echo $opportunity->start_date; ?></div>
                                    </div>
                                </div>
                                <?php ?>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.0013 2.66732C9.46797 2.66732 10.668 3.86732 10.668 5.33398C10.668 6.73398 9.26797 9.00065 8.0013 10.6007C6.73464 8.93398 5.33464 6.73398 5.33464 5.33398C5.33464 3.86732 6.53464 2.66732 8.0013 2.66732ZM8.0013 1.33398C5.8013 1.33398 4.0013 3.13398 4.0013 5.33398C4.0013 8.33398 8.0013 12.6673 8.0013 12.6673C8.0013 12.6673 12.0013 8.26732 12.0013 5.33398C12.0013 3.13398 10.2013 1.33398 8.0013 1.33398ZM8.0013 4.00065C7.26797 4.00065 6.66797 4.60065 6.66797 5.33398C6.66797 6.06732 7.26797 6.66732 8.0013 6.66732C8.73464 6.66732 9.33464 6.06732 9.33464 5.33398C9.33464 4.60065 8.73464 4.00065 8.0013 4.00065ZM13.3346 12.6673C13.3346 14.134 10.9346 15.334 8.0013 15.334C5.06797 15.334 2.66797 14.134 2.66797 12.6673C2.66797 11.8007 3.46797 11.0673 4.73464 10.534L5.13464 11.134C4.46797 11.4673 4.0013 11.8673 4.0013 12.334C4.0013 13.2673 5.8013 14.0007 8.0013 14.0007C10.2013 14.0007 12.0013 13.2673 12.0013 12.334C12.0013 11.8673 11.5346 11.4673 10.8013 11.134L11.2013 10.534C12.5346 11.0673 13.3346 11.8007 13.3346 12.6673Z" fill="black" />
                                        </svg>

                                    </div>
                                    <div class="ml-2 d-flex align-items-center" style="font-size:14px;font-weight:600;color:#012B33;">
                                        <div><?php echo $opportunity->location; ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php // third part 
                            ?>
                            <div class="my-2" style="font-size: 16px; font-weight: 400;">
                                <?php $lines = explode("\n", $opportunity->requirements);

                                // Display the first two lines
                                echo implode("\n", array_slice($lines, 0, 2)); ?>
                            </div>

                            <div class="d-flex my-4">
                                <?php
                                // Fetch skills for this opportunity
                                $skills_text = $skills->getAllSkillsForOpportunityById($opportunity->id);
                                // Debugging statement to check the value of $skills_text
                                if (!empty($skills_text)) {
                                    foreach ($skills_text as $skill) {
                                        echo '<div class="mx-2" style="background-color: #E7E7E8; border-radius: 4px; padding:2px 4px 2px 4px;font-size: 14px;font-weight: 400;">' . $skill->skill_name . '</div>';
                                    }
                                } else {
                                    echo '<div class="mx-2" style="font-size: 14px;font-weight: 400;">No skills found</div>';
                                }
                                ?>
                            </div>

                            <div></div>
                        </div>
                        <div class="card-footer d-flex">
                            <button class="viewOpportunityButton btn" data-id='<?php echo $opportunity->public_id ?>' style="width: 130px;height: 40px;background-color: #F5A800;padding: 8px 12px 8px 12px;font-size: 16px; font-weight: 700;border-radius: 4px;">
                                View Details
                            </button>
                            
                                  <div class="ml-2 match" data-id='<?php echo $opportunity->public_id ?>' style="width: 74px;height: 40px;background-color: #E7E7E8;padding: 8px 12px 8px 12px;font-size: 16px; font-weight: 700;border-radius: 4px;">
                                        Match
                                    </div>
                            


                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
    </div>
<?php
        }
?>
</div>


<?php
//view opportunity modal script
?>
<script>
    $(document).ready(function() {
        $('.viewOpportunityButton').on('click', function() {
            var opportunityId = $(this).data('id');
            console.log(opportunityId);
            $.ajax({
                url: 'modal_forms/fetct_opportunity_by_public_id.php', // Adjust the URL
                method: 'GET',
                data: {
                    id: opportunityId
                },
                success: function(response) {
                    $('.modal-content').html(response);
                    $('#viewmodal').modal('show');
                }
            });
        })
    })
</script>
<script>
   $(document).ready(function (){
    $('#solvermatchForm').submit(function(){
        event.preventDefault();
        var formData = $(this).serialize();
          $.ajax({
        type: 'POST',
        url: 'fr_handle_match_by_solver.php',
        data: formData,
        success: function(response) {
             $('#matchWithOpportunity').modal('hide');
          $('body').append(response)
          // Handle success response
          //$('#afterMatch').html(response); // Update modal content with response
         // $('#afterMatch').modal('show'); // Show the modal
        },
        error: function(xhr, status, error) {
          // Handle error
          console.error(xhr.responseText);
        }
      });
    })
    $('.match').on('click',function(){
        var opportunityPublic_id = $(this).data('id')
         var inputField = $('<input>');
      inputField.attr('name', 'opportunityPublic_id'); // Set name attribute
      inputField.attr('type', 'hidden');
      inputField.attr('id', 'opportunityPublic_id'); // Set type attribute
      inputField.val(opportunityPublic_id);

        $('#opportunity-id-input').append(inputField);
        $('#matchWithOpportunity').modal('show');
    })
   })
</script>
</body>


</html>