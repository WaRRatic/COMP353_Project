insert into 
      cosn.content_link_relationship (
        content_link_rel_id, 
        origin_content_id, 
        target_content_id
      )
    values
      (
        $content_link_rel_id, 
        $origin_content_id, 
        $target_content_id
      );