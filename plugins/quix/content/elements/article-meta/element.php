<!-- Article Meta Element -->
<QuixTemplate id="article-meta-template">
  <QuixHtml>
    <?php echo file_get_contents(__DIR__ . "/partials/html.twig") ?>
  </QuixHtml>
  <QuixStyle>
    <!-- Global Style -->
    <?php echo file_get_contents(JPATH_SITE . "/libraries/quixnxt/visual-builder/shared/global.twig") ?>
    <!-- Element Style -->
    <?php echo file_get_contents(__DIR__ . "/partials/style.twig") ?>
  </QuixStyle>
</QuixTemplate>
