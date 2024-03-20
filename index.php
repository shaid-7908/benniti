<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/header.php';
include 'inc/topbar.php';
Session::checkSession();

$pendingMsg = Session::get("pendingMsg");
if (isset($pendingMsg)) {
  echo $pendingMsg;
}

Session::set("pendingMsg", NULL);
//Load organizations
$userOrgs = $organizations->getAllOrganizationDataForUser(Session::get("userid"), $users);

// Check for first run
if (Session::get("firstrun") == 1 && isset($userOrgs) && is_array($userOrgs) && count($userOrgs) > 0) {
  Session::set("firstrun", 0);  //Skip first run if already in an org somehow
}
$firstrunUrl = "onBoard.php";
$onboardingData = Session::get("onboarding");
if (isset($onboardingData) && isset($onboardingData->resumeUrl) && ($onboardingData->resumeUrl == "index.php" || !isset($onboarding->completedUrl))) {
  Session::set("firstrun", 0);  //Finish first run flow
  Session::set("onboarding", null);
} else {
  if (isset($onboardingData->resumeUrl) && $onboardingData->resumeUrl != "")
    $firstrunUrl = $onboardingData->resumeUrl;
}
if (Session::get("firstrun") == 1) {
  header('Location:' . $firstrunUrl);
}
//Load Subscription status
$subscriptionExists = false;
if (!$subscriptions->checkSubscriptionExists(Session::get("userid"), "", $users, $organizations)) {
  foreach ($userOrgs as $thisOrg) {
    if ($subscriptions->checkSubscriptionExists(Session::get("userid"), $thisOrg->public_id, $users, $organizations)) {
      $subscriptionExists = true;
      break;
    }
  }
} else {
  $subscriptionExists = true;
}

//Load matches for this solver
$userMatches = $matches->getAllMatchDataForUserId(Session::get('userid'), $approvedOnly = false, $users, $organizations, $opportunities);
$totalMatches = sizeof($userMatches);

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
  <div class="xt-body-organization" style="color: black;padding: 10px;">
    <?php
    $username = Session::get('fullname');
    ?>
    <h4>Welcome, <?php echo $username; ?></h4>


    <?php

    $userOpportunity = $opportunities->getAllOpportunityDataForUser(Session::get('userid'), $users);
    $userSolverDetails = $solvers->getAllSolverDataForUser(Session::get('userid'), $users, $organizations);
    //if(!$userOpportunity || !$userSolverDetails){
    if (!$userOpportunity || !$userSolverDetails) {
    ?>
      <div style="display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-gap: 20px;">
        <div class="card" style="padding: 4px;">
          <div class="card-header" style="font-size: 20px; font-weight: 700; line-height: 28px; ">
            Organization onboarding
          </div>

          <div class="card-body">
            <p style="font-size: 16px; font-weight: 400; line-height: 24px;">Complete the following tasks to make the most of The Manufacturing Exchange.</p>
            <?php if ($userOrgs) { ?>

              <div class="d-flex p-2 my-4  align-items-center" style="border:2px solid #024552; border-radius: 4px; height:40px;font-size: 16px; font-weight: 600; line-height: 24px; background-color: #024552; color:White;">
                Create organization profile <span><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.00016 16.1701L4.83016 12.0001L3.41016 13.4101L9.00016 19.0001L21.0002 7.00009L19.5902 5.59009L9.00016 16.1701Z" fill="white" />
                  </svg>
                </span>
              </div>

            <?php } else { ?>
              <a href="organization.php?action=create_organization" style="color: inherit; text-decoration: none;">
                <div class="d-flex p-2 my-4  align-items-center" style="border:2px solid #024552; border-radius: 4px; height:40px;font-size: 16px; font-weight: 600; line-height: 24px; color:#024552;">
                  Create organization profile
                </div>
              </a>
            <?php } ?>
            <?php
            if ($userOpportunity) {
            ?>
              <div class="d-flex p-2 my-4  align-items-center" style="border:2px solid #024552;background-color: #024552; border-radius: 4px; height:40px;font-size: 16px; font-weight: 600; line-height: 24px; color:white;">
                Create your first Opportunity <span><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.00016 16.1701L4.83016 12.0001L3.41016 13.4101L9.00016 19.0001L21.0002 7.00009L19.5902 5.59009L9.00016 16.1701Z" fill="white" />
                  </svg>
                </span>
              </div>
            <?php } else { ?>
              <a href="opportunity.php?action=create_opportunity" style="color: inherit;text-decoration: none;">

                <div class="d-flex p-2 my-4  align-items-center" style="border:2px solid #024552; border-radius: 4px; height:40px;font-size: 16px; font-weight: 600; line-height: 24px; color:#024552;">
                  Create your first Opportunity
                </div>
              </a>
            <?php } ?>
            <?php if ($userSolverDetails) { ?>
              <div class="d-flex p-2  my-4 align-items-center" style="border:2px solid #024552;background-color:  #024552; border-radius: 4px; height:40px;font-size: 16px; font-weight: 600; line-height: 24px; color:white;">
                Create your Solver profile <span><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.00016 16.1701L4.83016 12.0001L3.41016 13.4101L9.00016 19.0001L21.0002 7.00009L19.5902 5.59009L9.00016 16.1701Z" fill="white" />
                  </svg>
                </span>
              </div>
            <?php } else { ?>

              <a href="solver.php?action=create_solver" style="color: inherit; text-decoration: none;">

                <div class="d-flex p-2  my-4 align-items-center" style="border:2px solid #024552; border-radius: 4px; height:40px;font-size: 16px; font-weight: 600; line-height: 24px; color:#024552;">
                  Create your Solver profile
                </div>
              </a>
            <?php } ?>
          </div>
        </div>
        <div class="card" style="padding: 4px;">
          <div class="card-header" style="font-size: 20px; font-weight: 700; line-height: 28px; ">
            Explore
          </div>
          <div class="card-body">
            <div class="my-4">
              <h6 style="font-size: 16px; font-weight: 700; line-height: 24px;">Find Solvers</h6>
              <p style="font-size: 16px; font-weight: 400; line-height: 24px;">Looking to solve a problem? Search for Solvers using some keywords. We’ll match you with the best resources.</p>
              <a href="findsolver.php" style="color: inherit; text-decoration: none;">
                <div style="font-size: 14px; font-weight: 700; line-height: 24px;padding:8px 12px 8px 12px;width: 120px;height:42px;background-color: #E7E7E8;">
                  Find Solvers
                </div>
              </a>
            </div>
            <div class="my-4">
              <h6 style="font-size: 16px; font-weight: 700; line-height: 24px;">Find Opportunities</h6>

              <p style="font-size: 16px; font-weight: 400; line-height: 24px;">Have skills and experience you want to share with others? We’ll match you with Seekers who need your expertise.</p>
              <a href="findopportunity.php" style="color: inherit; text-decoration: none;">

                <div style="font-size: 14px; font-weight: 700; line-height: 24px;padding:8px 12px 8px 12px;width: 160px;height:42px;background-color: #E7E7E8;">
                  Find Opportunities
                </div>
              </a>
            </div>
          </div>
        </div>

      </div>
    <?php
    } else {
      $user_real_id = $users->getRealId(Session::get('userid'));
      $newOpty = $opportunities->findLatestOpportunitiesinIndex($user_real_id);
      $gridActions = ["view"];
      $gridColumns = ["location", "organization"];

    ?>
      <div class="d-flex justify-content-between my-4">
        <div style="font-size: 24px; font-weight: 700;">Active Opportunities (<?php echo count($userOpportunity) ?>) </div>
        <div class="d-flex">
          <a href="opportunityList.php" style="color:inherit;text-decoration: none;">


            <div class="mr-2" style="width:88px;height:40px; border:2px solid #F5A800; padding:8px 12px 8px 12px;font-size: 16px; font-weight: 700; line-height: 24px;border-radius: 4px;">
              View All
            </div>
          </a>
          <a href="opportunity.php?action=create_opportunity" style="color:inherit;text-decoration: none;">
            <div style="width:215px;height:40px; border:2px solid #F5A800;background-color: #F5A800; padding:8px 12px 8px 12px;font-size: 16px; font-weight: 700; line-height: 24px;border-radius: 4px;">
              Create New Opportunity
            </div>
          </a>
        </div>
      </div>

    <?php
      $views->makeOpportunityGrid($newOpty, $gridColumns, $gridActions);
    } ?>
    <?php

    if (Session::get('roleid') == '1') {
    ?>
      <div class="card-body pr-2 pl-2">
        <div id="dashboard-container">
          <div id="dashboard-sidebar">
            <ul style="margin-top:10px">
              <li class="menu-action menu-action-highlight">
                <?php
                if ($totalMatches > 0) {
                  echo "<a href=\"matchList.php\">Review Matches</a> ";
                  echo " <i class=\"fas fa-certificate\">" . $totalMatches . "</i>" . PHP_EOL;
                } else {
                  echo "No matches yet!" . PHP_EOL;
                }
                ?>
              </li>
              <li class="menu-action menu-action-highlight"><a href="opportunityList.php?query">Find Opportunities</a></li>
              <li class="menu-action menu-action-highlight"><a href="solverList.php?query">Find Solvers</a></li>
            </ul>
            <ul style="margin-top: 28px;">
              <li class="menu-action"><a href="organizationList.php?userid=<?php echo Session::get("userid"); ?>">My Organizations</a></li>
              <li class="menu-action"><a href="opportunityList.php">My Opportunities</a></li>
            </ul>
          </div>
          <div id="dashboard-content">


            <br />
            <h5>
              The Manufacturing Exchange™ is where you can match opportunities with industry professionals, ensuring successful project outcomes.
            </h5>
            <?php
            if ($totalMatches == 1) {
              echo "<h5 style=\"padding-top:12px; padding-bottom:12px\">Bennit's AI has found 1 potential match for you to review!</h5>" . PHP_EOL;
            } elseif ($totalMatches > 1) {
              echo "<h5 style=\"padding-top:12px; padding-bottom:12px\">Bennit's AI has found " . $totalMatches . " potential matches for you to review!</h5>" . PHP_EOL;
            } else {
              echo "<h5>&nbsp;</h5>" . PHP_EOL;
            }
            if (!$subscriptions->checkSubscriptionExistsAnywhere(Session::get('userid'), $users, $organizations)) {
              echo "<div class='subscription-status'><span class='link-action'><a href='subscription.php?action=create_subscription'>Subscribe now</a></span> to make and review matches...</div>" . PHP_EOL;
            } else {
              echo "<div class='subscription-status'>You have an <a href='subscriptionList.php'>active subscription</a>, and are able to make and review matches...</div>" . PHP_EOL;
            }
            ?>
            <ul>
              <?php
              if (sizeof($userOrgs) < 1) {
                echo "<li>You don't belong to any Organizations yet. <span class='link-action'><a href='organization.php'>Create an Organization</a></span></li>" . PHP_EOL;
              } else {
                foreach ($userOrgs as $org) {
                  $opty = $opportunities->getAllOpportunityData($org->public_id, $organizations);
                  if (sizeof($opty) == 1) {
                    echo "<li>Your organization <b>" . $org->orgname . "</b> has <span class='link-action'><a href='opportunityList.php?org=" . $org->public_id . "'>" . sizeof($opty) . " active Opportunity.</a></span></li>" . PHP_EOL;
                  } elseif (sizeof($opty) > 1) {
                    echo "<li>Your organization <b>" . $org->orgname . "</b> has <span class='link-action'><a href='opportunityList.php?org=" . $org->public_id . "'>" . sizeof($opty) . " active Opportunities.</a></span></li>" . PHP_EOL;
                  } else {
                    if ($org->org_level > 0 && $org->org_level < 3)
                      echo "<li>Your organization <b>" . $org->orgname . "</b> doesn't have any Opportunities yet. <span class='link-action'><a href='opportunity.php?action=create_opportunity&orgid=" . getIfSet($org, "public_id") . "'>Create an Opportunity</a></span></li>" . PHP_EOL;
                  }
                  if ($org->id == 1 && Session::get('roleid') <= 2) {
                    $orgSolvers = $solvers->getSolverProfilesByRealOrgId(1);
                    if (count($orgSolvers) > 0) {
                      echo "<li>Solvers in your organization: </li><ul>" . PHP_EOL;
                      foreach ($orgSolvers as $thisSolver) {
                        $useHeadline = $thisSolver->headline;
                        $useHeadline = (strlen($useHeadline) > 43) ? substr($useHeadline, 0, 40) . '...' : $useHeadline;
                        echo "<li><a href='solverView.php?solverid=" . $thisSolver->public_id . "'>" . $useHeadline . "</a></li>" . PHP_EOL;
                      }
                      echo "</ul>" . PHP_EOL;
                    }
                  } else {
                    $solver = $solvers->getSolverProfileByOrgIdAndUserId($org->public_id, Session::get("userid"), $organizations, $users);
                    if (isset($solver) && isset($solver->public_id)) {
                      echo "<li>Your organization <b>" . $org->orgname . "</b> has an active <span class='link-action'><a href='solverView.php?solverid=" . getIfSet($solver, "public_id") . "'>Solver Profile.</a></span></li>" . PHP_EOL;
                    } else {
                      if ($org->id == 1 && ($org->org_level < 0 || $org->org_level > 3))  //org 1 is a special case
                        echo "<li>You don't have a Solver Profile in the organization <b>" . $org->orgname . "</b>. <span class='link-action'><a href='solver.php?action=create_solver&orgid=" . getIfSet($org, "public_id") . "'>Create your Solver Profile now</a></span></li>" . PHP_EOL;
                      else
                        echo "<li>Your organization <b>" . $org->orgname . "</b> doesn't have a Solver profile yet. <span class='link-action'><a href='solver.php?action=create_solver&orgid=" . getIfSet($org, "public_id") . "'>Create a Solver Profile</a></span></li>" . PHP_EOL;
                    }
                  }
                  echo "<br>" . PHP_EOL;
                }
              }
              ?>
            </ul>
          </div>
        </div>
      </div>
    <?php
    };
    ?>
    <div class="p-4 mt-4" style="display: grid;grid-template-columns: repeat(2, 1fr); 
         gap: 20px;border: 1px solid rgba(0, 0, 0, .125);
         border-radius: 0.25rem;">
      <?php
      $userOrginfo = $organizations->getAllOrganizationDataForUser(Session::get('userid'), $users);
       foreach($userOrginfo as $org):
      ?>
      <div class="card p-2">
        <h1 class="inter-font font-20 font-700">Organization <?php echo $org->orgname ?></h1>
        <p><?php echo $org->public_id ?></p>
        <div class="inter-font font-16 font-700">Solver profiles under this organization</div>
        <?php
        $solverInfo = $solvers->getAllSolverProfileByOrgIdAndUserId($org->public_id,Session::get('userid'),$organizations,$users);
        if(empty($solverInfo)){
        ?>
        <div class="my-4" style="font-size: 14px; font-weight: 700; line-height: 24px;padding:8px 12px 8px 12px;height:42px;background-color: #E7E7E8;">
        This organization dose not have Solver profile.
      </div>

        <?php
        }else{
        foreach($solverInfo as $solver):
        ?>
        <div class="my-4" style="font-size: 14px; font-weight: 700; line-height: 24px;padding:8px 12px 8px 12px;height:42px;background-color: #E7E7E8;">
           <?php
                       $useHeadline = $solver->headline;
                       $useHeadline = (strlen($useHeadline) > 33) ? substr($useHeadline,0,30).'...' : $useHeadline;
                       echo $useHeadline;
                     ?>
        </div>
        <?php
        endforeach;
        }
        ?>
        <div style="display: flex;">
           <a href="solver.php?action=create_solver" style="color: inherit; text-transform: none;">
             <div style="font-size: 14px; font-weight: 700; line-height: 24px;padding:8px 12px 8px 12px;width: 160px;height:42px;background-color: #F5A800;">
               Create more solver
              </div>
           </a>

          <div class="ml-2" style="font-size: 14px; font-weight: 700; line-height: 24px;padding:8px 12px 8px 12px;height:42px;background-color: #E7E7E8;">
            View Opportunities
          </div>
        </div>

      </div>
       <?php
       endforeach;
       ?>
    </div>
  </div>




</html>