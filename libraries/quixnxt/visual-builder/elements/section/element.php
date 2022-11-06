<QuixTemplate id="section-template">
  <QuixScript>
    <!-- Section Script -->
    <?php echo file_get_contents(__DIR__ . "/partials/script.twig") ?>
  </QuixScript>
  <QuixStyle>
    <!-- Global Style -->
    <?php echo file_get_contents(__DIR__ . "/../../shared/global.twig") ?>
    <!-- Section Style -->
    <?php echo file_get_contents(__DIR__ . "/partials/style.twig") ?>
  </QuixStyle>
</QuixTemplate>
