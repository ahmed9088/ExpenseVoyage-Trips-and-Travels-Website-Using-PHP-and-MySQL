    <footer class="footer mt-auto py-3 bg-white border-top">
        <div class="container-fluid text-center">
            <span class="text-muted small">© 2026 ExpenseVoyage. Agent Navigation Module v1.0</span>
        </div>
    </footer>
</div> <!-- End of modern-main -->
</div> <!-- End of admin-wrapper -->

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sidebar Toggle
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        document.querySelector('.modern-sidebar').classList.toggle('active');
    });

    // Auto-dismiss alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 4000);
</script>
</body>
</html>
