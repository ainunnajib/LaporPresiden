<?php
        class qa_lappres_admin {

                function allow_template($template)
                {
                        return ($template!='admin');
                }

                function admin_form()
                {
                //      Create the form for display
						$ok = null;
                        if(qa_clicked('download_terbaru')) {
                                
                                switch (qa_post_text('status')) {
                                    case 'terbaru':
                                        $title = 'Terbaru';

                                        $data = qa_db_read_all_assoc(
                                            qa_db_query_sub(
                                                "SELECT *, post.created as tanggal FROM ^posts as post,^users as usr WHERE post.userid = usr.userid AND post.type='Q' ORDER BY post.created DESC"
                                            )
                                        );
                                    break;
                                    case 'range':
                                        $title = 'Range';
                                        $bulan = qa_post_text('bulan');
                                        $tahun = qa_post_text('tahun');

                                        $data = qa_db_read_all_assoc(
                                            qa_db_query_sub(
                                                "SELECT *, post.created as tanggal FROM ^posts as post,^users as usr WHERE post.userid = usr.userid AND post.type='Q' AND post.created LIKE '%$tahun-$bulan%' ORDER BY post.created DESC"
                                            )
                                        );
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
                                        $result .= '<img src="assets/img/logo.png" style="width:250;float:left;" />';
                                        $result .= '<h1 style="text-align:center;">Laporan '.$title.' dari LaporPresiden.org</h1>';
                                        $result .= '<hr /><br /><br />';
                                        $no = 1;
                                        $result .= '<table width="100%">';
                                        if ($data != false) {
                                            foreach ($data as $key => $value) {
                                                $result .= '<tr>';
                                                    $result .= '<td>'.$no.'. </td>';
                                                    $result .= '<td>'.$value['tanggal'].'</td>';
                                                    $result .= '<td>'.$value['title'].'</td>';
                                                    $result .= '<td>'.$value['handle'].'</td>';
                                                $result .= '</tr>';
                                                $result .= '<tr>';
                                                    $result .= '<td colspan="4">'.$value['content'].'</td>';
                                                $result .= '</tr>';
                                                $result .= '<tr>';
                                                    $result .= '<td colspan="4"><hr/></td>';
                                                $result .= '</tr>';
                                                $no++;
                                            }
                                        }else{
                                            $result .= 'Data Kosong';
                                        }
                                        $result .= '</table>';
                                    $result .= '</body>';
                                $result .= '</html>';

                                $pdf_output= 'laporan_terbaru'.date('Y-m-d').'.pdf';
                                $title = 'Laporan Terbaru | LaporPresiden.org';
                                downloadPDF($result, $title, $pdf_output);
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
                        //===========================//
                        $fields[] = array(
                                'label' => 'Download Laporan range',
                                'value' => '<input type="radio" name="status" value="range">',
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
