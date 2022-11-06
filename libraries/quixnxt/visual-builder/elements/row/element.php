<!-- row template -->
<QuixTemplate id="row-template">
  <QuixStyle>
    <!-- Global Style -->
    <?php echo file_get_contents(__DIR__ . "/../../shared/global.twig") ?>
    <!-- Section Style -->
    <?php echo file_get_contents(__DIR__ . "/partials/style.twig") ?>
  </QuixStyle>
</QuixTemplate>
