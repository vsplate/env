DROP DATABASE IF EXISTS vulnspy_test;
CREATE DATABASE vulnspy_test;
USE vulnspy_test;

CREATE TABLE t (
  c varchar(16) not null,
  index (c)
);

CREATE TABLE t2 (
  c varchar(16) not null,
  index (c)
);

INSERT INTO t VALUES ('a'), ('b'), ('c'), ('d'), ('e'), ('f'), ('g'), ('h'), ('i'), ('j'), ('k'), ('l'), ('m'), ('n'), ('o'), ('p'), ('q'), ('r'), ('s'), ('t'), ('u'), ('v'), ('w'), ('x'), ('y'), ('z');

INSERT INTO t2 VALUES ('a'), ('b'), ('c'), ('d'), ('e'), ('f'), ('g'), ('h'), ('i'), ('j'), ('k'), ('l'), ('m'), ('n'), ('o'), ('p'), ('q'), ('r'), ('s'), ('t'), ('u'), ('v'), ('w'), ('x'), ('y'), ('z');

DROP DATABASE IF EXISTS vulnspy_tables;
CREATE DATABASE vulnspy_tables;
USE vulnspy_tables;

-- This table can cause an infinite nibbling loop.
CREATE TABLE `inv` (
  `tee_id` int(11) NOT NULL,
  `on_id` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  KEY `index_inv_on_on_id` (`on_id`),
  KEY `index_inv_on_tee_id_and_on_id` (`tee_id`,`on_id`)
);

INSERT INTO inv (tee_id, on_id) VALUES
  (1, 1), (1, 2), (1, 3), (1, 4), (1, 5),         (1, 7), (1, 8), (1, 9),
  (2, 1), (2, 2), (2, 3),         (2, 5), (2, 6), (2, 7), (2, 8),
  (3, 1), (3, 2), (3, 3), (3, 4),
                  (4, 3), (4, 4), (4, 5), (4, 6), (4, 7), (4, 8), (4, 9),
  (5,1),
  (6, 1), (6, 2), (6, 3), (6, 4), (6, 5), (6, 6), (6, 7), (6, 8), (6, 9),
  (7, 1), (7, 2), (7, 3), (7, 4), (7, 5), (7, 6), (7, 7), (7, 8), (7, 9);
