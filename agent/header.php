<nav class="navbar modern-navbar px-4 py-3">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <button class="btn btn-link text-white d-lg-none me-2" id="sidebarToggle">
                <i class="fa-solid fa-bars-staggered"></i>
            </button>
            <div class="search-bar d-none d-md-flex">
                <i class="fa-solid fa-magnifying-glass me-2 text-muted"></i>
                <input type="text" placeholder="Search tasks..." class="bg-transparent border-0 text-white">
            </div>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="btn btn-link text-white position-relative p-0" data-bs-toggle="dropdown">
                    <i class="fa-regular fa-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-primary border border-light rounded-circle"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><h6 class="dropdown-header">Notifications</h6></li>
                    <li><a class="dropdown-item small" href="#">New booking assigned</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-center small text-indigo" href="#">View All</a></li>
                </ul>
            </div>
            
            <div class="dropdown">
                <div class="user-pill d-flex align-items-center gap-2" data-bs-toggle="dropdown" role="button">
                    <div class="user-info text-end d-none d-sm-block">
                        <div class="fw-bold text-white fs-xs"><?php echo htmlspecialchars($agent_name); ?></div>
                        <div class="text-muted fs-xxs">Mission Agent</div>
                    </div>
                    <img src="<?php echo htmlspecialchars($agent_img); ?>" class="rounded-circle shadow-sm border border-2 border-indigo-light" width="40" height="40">
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item" href="profile.php"><i class="fa-solid fa-user-gear me-2"></i>Profile Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../logout.php"><i class="fa-solid fa-power-off me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
