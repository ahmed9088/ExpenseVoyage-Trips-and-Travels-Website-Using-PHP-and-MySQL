<?php
// Close modern-main div if it wasn't closed in the page (some pages might match open/close)
// But to be safe, pages should likely close their own main, or we close it here.
// Looking at dashboard.php, it has </main> at the end.
// So we just need to close the wrappers.
?>
    <!-- Close modern-content-wrapper -->
    </div>
<!-- Close admin-wrapper -->
</div>

<!-- Core Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<!-- Initialize DataTables (Global Fallback) -->
<script>
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('.modern-table').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records..."
            }
        });
    }
});
</script>

</body>
</html>
