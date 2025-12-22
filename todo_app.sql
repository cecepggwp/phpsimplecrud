SELECT id_member, user_member, nama_member, nama_vip, nama_provinsi, alamat_member, email_member, telp_member, status_member
                  FROM db_simplecrud.`tb_member`
                  JOIN tb_vip ON kode_vip = vip_member
                  JOIN tb_provinsi ON id_provinsi = provinsi;
                 
SELECT t.id, t.name, t.description, t.deadline, t.status, 
                         c.name AS category_name, t.category_id
                  FROM tasks t
                  LEFT JOIN categories c ON t.category_id = c.id
                  ORDER BY t.status ASC, t.deadline ASC, t.id DESC;