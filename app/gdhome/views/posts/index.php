<p>Here is a list of all posts:</p>

<?php foreach($posts as $post) { ?>
  <p>
    <?php echo $post->Temp; ?>
    <a href='?controller=posts&action=show&TimeStamp=<?php echo $post->TimeStamp; ?>'> <?php echo $post->TimeStamp; ?> See content</a>
  </p>
<?php } ?>