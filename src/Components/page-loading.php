<style>
  @keyframes page-load-spin {
    to { transform: rotate(360deg); }
  }
</style>
<div id="page-loading" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(255,255,255,.8)">
  <div style="width:2.5rem;height:2.5rem;border:4px solid var(--color-border,#e5e7eb);border-top-color:var(--color-brand,#ba1229);border-radius:50%;animation:page-load-spin 1s linear infinite"></div>
</div>
<script>
  (function() {
    var el = document.getElementById('page-loading');
    window.__showLoading = function() {
      if (!el) return;
      el.style.display = 'flex';
    };
    document.addEventListener('DOMContentLoaded', function() {
      document.addEventListener('submit', function(e) {
        if (!e.defaultPrevented) window.__showLoading();
      });
    });
  })();
</script>
