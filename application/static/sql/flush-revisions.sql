# Before: 163118
#
DELETE a,b,c FROM edm_12_posts a 
  LEFT JOIN edm_12_term_relationships b ON (a.ID = b.object_id)
  LEFT JOIN edm_12_postmeta c ON (a.ID = c.post_id)
  WHERE a.post_type = 'revision'