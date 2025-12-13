    </div> <!-- end content -->
</div> <!-- end d-flex -->

<footer class="text-center py-3 mt-10 border-top" style="background-color: #1e293b; color: #cbd5e1;">
    © <?= date("Y"); ?> Arsip Digital — All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('mainContent');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('expanded');
    });
</script>
</body>
</html>
