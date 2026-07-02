<?php
$query = '
    SELECT * FROM table WHERE
    ((p.nik IS NULL OR p.nik = '') AND (p.nip IS NULL OR p.nip = ''))
';
