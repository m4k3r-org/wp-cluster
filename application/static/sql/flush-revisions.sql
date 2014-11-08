##
## edm_12_postmeta            - 163118  162960
## edm_12_posts               - 75535   63928
## edm_12_term_relationships  - 10059   10059
##

DELETE a,b,c FROM edm_12_posts a
  LEFT JOIN edm_12_term_relationships b ON (a.ID = b.object_id)
  LEFT JOIN edm_12_postmeta c ON (a.ID = c.post_id)
  WHERE a.post_type = 'revision'