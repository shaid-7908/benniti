<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
  include 'inc/header.php';
  include 'inc/topbar.php';
  Session::checkSession();
  if (!checkUserAuth('admin_panel', Session::get('roleid'))) {
    error_log("A user with id " . Session::get('userid') . " and role " . Session::get('userid') . " attempted to access the admin panel without sufficient permissions.");
    header("Location:index.php");
  }

  $pendingMsg = Session::get("pendingMsg");
  if (isset($pendingMsg)) {
    echo $pendingMsg;
  }
  Session::set("pendingMsg", NULL);
?>
<div class="xt-card-organization">
  <div class="xt-sidebar-organization">

  </div>
  <div class= "xt-body-organization" style="color: black;">
      <div class="card ">
    <div class="card-header">
      <h3><i class="fas fa-lock mr-2"></i>Admin</h3>
    </div>
    <div class="card-body" style="clear:both;">
    <ul class="icon-list">
    <li class="icon-admin">
        <a href="https://analytics.google.com/analytics/web/#/p412824326/reports/intelligenthome">
          <span>
          &nbsp; <i class="far fa-chart-bar"></i><br/>
          Google Analytics
          </span>
        </a>
      </li>
      <li class="icon-admin">
        <a href="https://dashboard.stripe.com/customers">
          <span>
          &nbsp;<i class="fab fa-cc-stripe"></i><br/>
          Stripe Customers
          </span>
        </a>
      </li>
      <li class="icon-admin">
        <a href="subscriptionList.php?as=admin">
          <span>
          &nbsp; <i class="fas fa-shopping-cart"></i><br/>
          Subscriptions
          </span>
        </a>
      </li>
      <li class="icon-admin">
        <a href="userList.php?as=admin">
          <span>
          &nbsp;<i class="fab fa-500px mr-2"></i><br/>
          Users
          </span>
        </a>
      </li>
      <li class="icon-admin">
        <a href="organizationList.php?as=admin">
          <span>
          &nbsp; <i class="fas fa-building mr-2"></i><br/>
          Organizations
          </span>
        </a>
      </li>
      <li class="icon-admin">
        <a href="opportunityList.php?as=admin">
          <span>
          &nbsp; <i class="fas fa-lightbulb mr-2"></i><br/>
          Opportunities
          </span>
        </a>
      </li>
      <li class="icon-admin">
        <a href="solverList.php?as=admin">
          <span>
          &nbsp; <i class="fas fa-briefcase mr-2"></i><br/>
          Solvers
          </span>
        </a>
      </li>
      <li class="icon-admin">
        <a href="matchList.php?as=admin">
          <span>
          &nbsp; <i class="fas fa-plug mr-2"></i><br/>
          Matches
          </span>
        </a>
      </li>
      <li class="icon-admin">
        <a off="skillList.php?as=admin">
          <span>
          &nbsp; <i class="fas fa-hammer mr-2"></i><br/>
          Skills
          </span>
        </a>
      </li>
      <li class="icon-admin">
        <a off="skillList.php?as=admin">
          <span>
          &nbsp; <i class="fas fa-book mr-2"></i><br/>
          Certifications
          </span>
        </a>
      </li>
      <li class="icon-admin">
        <a href="testMode.php">
          <span>
          &nbsp; <i class="fas fa-flask"></i><br/>
          Test Mode
          </span>
        </a>
      </li>
      
    </ul>
    </div>
  </div>
  </div>

</div>
 
<?php
  include 'inc/footer.php';
?>
</html>