<!-- Form Element -->
<QuixTemplate id="form-template">
  <QuixHtml>
    <!-- forms macro -->
    <?php echo file_get_contents(__DIR__ . "/partials/macro.twig") ?>
    <!-- content view -->
    <?php echo file_get_contents(__DIR__ . "/partials/html.twig") ?>
  </QuixHtml>
  <QuixStyle>
    <!-- Global Style -->
    <?php echo file_get_contents(__DIR__ . "/../../shared/global.twig") ?>
    <!-- Element Style -->
    <?php echo file_get_contents(__DIR__ . "/partials/style.twig") ?>
  </QuixStyle>
  <QuixScript>
    <?php echo file_get_contents(__DIR__ . "/partials/script.twig") ?>
  </QuixScript>
</QuixTemplate>
