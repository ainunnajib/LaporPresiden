<?php
    class qa_lappres_admin {

        function allow_template($template)
        {
            return ($template!='admin');
        }

        function bulan($param){
            switch ($param) {
                case '01':
                    $return = 'Januari';
                break;
                case '02':
                    $return = 'Februari';
                break;
                case '03':
                    $return = 'Maret';
                break;
                case '04':
                    $return = 'April';
                break;
                case '05':
                    $return = 'Mei';
                break;
                case '06':
                    $return = 'Juni';
                break;
                case '07':
                    $return = 'Juli';
                break;
                case '08':
                    $return = 'Agustus';
                break;
                case '09':
                    $return = 'September';
                break;
                case '10':
                    $return = 'Oktober';
                break;
                case '11':
                    $return = 'November';
                break;
                case '12':
                    $return = 'Desember';
                break;
                return $return;
            }
        }
        function admin_form()
        {
            //Create the form for display
			$ok = null;
            if(qa_clicked('download_terbaru')) {
                    
                    switch (qa_post_text('status')) {
                        case 'terbaru':
                            $title = 'Terbaru';
                            $title_head = 'Terbaru';

                            $data = qa_db_read_all_assoc(
                                qa_db_query_sub(
                                    "SELECT *, post.created as tanggal FROM ^posts as post,^users as usr WHERE post.userid = usr.userid AND post.type='Q' ORDER BY post.created DESC"
                                )
                            );
                        break;
                        case 'range':
                            $bulan = qa_post_text('bulan');
                            $tahun = qa_post_text('tahun');
                            $tags = qa_post_text('tags_range');
                            $title = 'Sesuai bulan';
                            switch (qa_post_text('jenis_laporan')) {
                                case 'terbaru':
                                    if($tags == 'all'){
                                        $title_head = 'Pada bulan '.$bulan.' tahun '.$tahun.' Terbaru';
                                        $data = qa_db_read_all_assoc(
                                            qa_db_query_sub(
                                                "SELECT *, post.created as tanggal FROM ^posts as post,^users as usr WHERE post.userid = usr.userid AND post.type='Q' AND post.created LIKE '%$tahun-$bulan%' ORDER BY post.created DESC"
                                            )
                                        );   
                                    }else{
                                        $nama_tags = qa_db_read_all_assoc(
                                            qa_db_query_sub(
                                                "SELECT * FROM ^words WHERE wordid = '$tags'"
                                            )
                                        );
                                        foreach ($nama_tags as $key => $value) {
                                            $title_head = 'Pada bulan '.$bulan.' tahun '.$tahun.' Terbaru dan dengan tags "'.$value['word'].'"';
                                        }
                                        
                                        $data = qa_db_read_all_assoc(
                                            qa_db_query_sub(
                                                "SELECT *, post.created as tanggal FROM ^posts as post,^users as usr, ^posttags as post_tag WHERE post.userid = usr.userid AND post.type='Q' AND post_tag.postid = post.postid AND post_tag.wordid = '$tags' AND post.created LIKE '%$tahun-$bulan%' ORDER BY post.created DESC"
                                            )
                                        );
                                    }
                                    
                                break;
                                case 'votes':
                                    if($tags == 'all'){
                                        $title_head = 'Pada bulan '.$bulan.' tahun '.$tahun.' Sesuai votes';
                                        $data = qa_db_read_all_assoc(
                                            qa_db_query_sub(
                                                "SELECT *, post.created as tanggal FROM ^posts as post,^users as usr WHERE post.userid = usr.userid AND post.type='Q' AND post.created LIKE '%$tahun-$bulan%' ORDER BY post.netvotes DESC"
                                            )
                                        );
                                    }else{
                                        $nama_tags = qa_db_read_all_assoc(
                                            qa_db_query_sub(
                                                "SELECT * FROM ^words WHERE wordid = '$tags'"
                                            )
                                        );
                                        foreach ($nama_tags as $key => $value) {
                                            $title_head = 'Pada bulan '.$bulan.' tahun '.$tahun.' Sesuai votes dan dengan tags "'.$value['word'].'"';
                                        }
                                        
                                        $data = qa_db_read_all_assoc(
                                            qa_db_query_sub(
                                                "SELECT *, post.created as tanggal FROM ^posts as post,^users as usr, ^posttags as post_tag WHERE post.userid = usr.userid AND post.type='Q' AND post_tag.postid = post.postid AND post_tag.wordid = '$tags' AND post.created LIKE '%$tahun-$bulan%' ORDER BY post.netvotes DESC"
                                            )
                                        );
                                    }
                                break;
                            }
                        break;
                        case 'tags':
                            $tags = qa_post_text('tags');
                            $nama_tags = qa_db_read_all_assoc(
                                qa_db_query_sub(
                                    "SELECT * FROM ^words WHERE wordid = '$tags'"
                                )
                            );
                            foreach ($nama_tags as $key => $value) {
                                $title = 'Sesuai Tags "'.$value['word'].'"';
                                $title_head = 'Sesuai Tags "'.$value['word'].'"';
                            }
                            
                            $data = qa_db_read_all_assoc(
                                qa_db_query_sub(
                                    "SELECT *, post.created as tanggal FROM ^posts as post,^users as usr, ^posttags as post_tag WHERE post.userid = usr.userid AND post.type='Q' AND post_tag.postid = post.postid AND post_tag.wordid = '$tags' ORDER BY post.created DESC"
                                )
                            );
                        break;
                    }

                    $result = '<html>';
                        $result .= '<body>';
                            if(qa_post_text('type') == 'pdf'){
                                $result .= '<img src="/assets/img/logo-report.png" style="width:250px;float:left;" />';
                                $result .= '<h1 style="text-align:right;">Laporan '.$title_head.' dari LaporPresiden.org</h1>';    
                            }else{
                                $result .= '<h1 style="text-align:center;">Laporan '.$title_head.' dari LaporPresiden.org</h1>';
                            }
                            
                            $result .= '<hr /><br /><br />';
                            $no = 1;
                            $result .= '<table width="100%">';
                            if ($data != false) {
                                foreach ($data as $key => $value) {
                                    $result .= '<tr>';
                                        $result .= '<td>'.$no.'. </td>';
                                        $result .= '<td> Tanggal : '.$value['tanggal'].'<br />';
                                        $result .= 'Pelapor : '.$value['handle'].'<br />';
                                        $result .= 'Vote : '.$value['netvotes'].'<br />';
                                        $result .= 'Judul : '.$value['title'].'</td>';
                                    $result .= '</tr>';
                                    $result .= '<tr>';
                                        $result .= '<td></td>';
                                        $result .= '<td>'.$value['content'].'</td>';
                                    $result .= '</tr>';
                                    $result .= '<tr>';
                                        $result .= '<td colspan="2"><hr/></td>';
                                    $result .= '</tr>';
                                    $no++;
                                }
                            }else{
                                $result .= 'Data Kosong';
                            }
                            $result .= '</table>';
                        $result .= '</body>';
                    $result .= '</html>';

                    switch (qa_post_text('type')) {
                        case 'pdf':
                            $pdf_output= 'report_LaporPresiden'.date('Y-m-d').'.pdf';
                            $titles = 'Laporan '.$title.' | LaporPresiden.org';
                            downloadPDF($result, $titles, $pdf_output);
                        break;
                        case 'excel':
                            $filename2= 'report_LaporPresiden'.date('Y-m-d').'.xls';
                            $titles = 'Laporan '.$title.' | LaporPresiden.org';
                            // downloadPDF($result, $titles, $pdf_output);
                            header("Content-type: application/msexcel");
                            header("Content-Disposition: attachment; filename=$filename2");
                            header("Pragma: no-cache");
                            header("Expires: 0");
                            print $result;
                        break;
                        case 'word':
                            $filename1= 'report_LaporPresiden'.date('Y-m-d').'.doc';
                            $titles = 'Laporan '.$title.' | LaporPresiden.org';
                            // downloadPDF($result, $titles, $pdf_output);
                            header("Content-type: application/msword");
                            header("Content-Disposition: attachment; filename=$filename1");
                            header("Pragma: no-cache");
                            header("Expires: 0");
                            print $result;
                        break;
                        
                    }
            }

            $fields = array();
            $fields[] = array(
                'type' => 'blank',
            );
            $fields[] = array(
                'label' => 'Download Laporan terbaru',
                'value' => '<input type="radio" name="status" value="terbaru" CHECKED>',
                'type' => 'static',
            );
            $fields[] = array(
                'type' => 'blank',
            );
            //================================//
            $fields[] = array(
                'label' => 'Download Laporan sesuai tag',
                'value' => '<input type="radio" name="status" value="tags">',
                'type' => 'static',
            );
            
            $data_tags = qa_db_read_all_assoc(
                qa_db_query_sub(
                    "SELECT *, post_tag.wordid as word_id FROM ^posttags as post_tag, ^words as word WHERE post_tag.wordid = word.wordid GROUP BY post_tag.wordid"
                )
            );
            $tags = array();
            foreach ($data_tags as $key => $value) {
                $tags .= '<option value="'.$value['word_id'].'">'.$value['word'].'</option>';
            }
            $fields[] = array(
                'label' => 'Pilih Tags',
                'value' => '<select name="tags">
                                '.$tags.'
                            </select>',
                'type' => 'static',
            );
            $fields[] = array(
                'type' => 'blank',
            );
            //===========================//
            $fields[] = array(
                'label' => 'Download Laporan range',
                'value' => '<input type="radio" name="status" value="range">',
                'type' => 'static',
            );
            $fields[] = array(
                'label' => 'Pilih Jenis Laporan',
                'value' => 'Jenis Laporan <select name="jenis_laporan">
                                <option value="terbaru">Terbaru</option>
                                <option value="votes">Votes</option>
                            </select>',
                'type' => 'static',
            );
            $data_tags = qa_db_read_all_assoc(
                qa_db_query_sub(
                    "SELECT *, post_tag.wordid as word_id FROM ^posttags as post_tag, ^words as word WHERE post_tag.wordid = word.wordid GROUP BY post_tag.wordid"
                )
            );
            $tags = array();
            foreach ($data_tags as $key => $value) {
                $tags .= '<option value="'.$value['word_id'].'">'.$value['word'].'</option>';
            }
            $fields[] = array(
                'label' => 'Pilih Tags',
                'value' => '<select name="tags_range">
                                <option value="all">All</option>
                                '.$tags.'
                            </select>',
                'type' => 'static',
            );
            $fields[] = array(
                'label' => 'Pilih Bulan',
                'value' => 'Bulan<select name="bulan">
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>',
                'type' => 'static',
            );
            $tahun = array();
            for ($i=2014; $i < 2026; $i++) { 
                $tahun .= '<option value="'.$i.'">'.$i.'</option>';
            }
            $fields[] = array(
                'label' => 'Pilih Tahun',
                'value' => '<select name="tahun">
                                '.$tahun.'
                            </select>',
                'type' => 'static',
            );
            $fields[] = array(
                'type' => 'blank',
            );
            //==========================================//
            $fields[] = array(
                'label' => 'Pilih Type Laporan',
                'value' => '<select name="type">
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                                <option value="word">Word</option>
                            </select>',
                'type' => 'static',
            );
            $fields[] = array(
                'type' => 'blank',
            );

            return array(
                'ok' => ($ok && !isset($error)) ? $ok : null,
                'fields' => $fields,
                'buttons' => array(
                        array(
                            'label' => 'Download',
                            'tags' => 'NAME="download_terbaru"',
                        ),
                ),
            );
        }
    }

/*
        Omit PHP closing tag to help avoid accidental output
*/
