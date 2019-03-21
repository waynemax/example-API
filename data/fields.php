<?php
    function fields_getBy($case, $type){
        $defineFields = array(
            'posts' => [
                'all' => [
                    "id",
                    "real_author_id",
                    "object_id",
                    "author_type",
                    "create_date",
                    "repost_post_id",
                    "count_reposts",
                    "count_likes",
                    "main_type",
                    "header_text",
                    "content_text",
                    "images",
                    "videos",
                    //"audios",
                    //"docs",
                    "count_donate",
                    "tags",
                    "advertising",
                    "date_begin",
                    "date_end",
                    "count_comments",
                    "moderated_type",
                    "count_complaints",
                    "age_limit_top",
                    "age_limit_bottom",
                    "for_confession_type",
                    "for_earning_type",
                    "for_education_type",
                    "for_social_status_type",
                    "for_gender_type",
                    "for_family_type",
                    "for_childrens_type",
                    "activation_type"
                ],

                'default' =>[
                    "id",
                    "real_author_id",
                    "author_type",
                    "create_date",
                    "repost_post_id",
                    "count_reposts",
                    "count_likes",
                    "main_type",
                    "header_text",
                    "content_text",
                    "images",
                    "videos",
                    //"audios",
                    //"docs",
                    "count_donate",
                    "tags",
                    "count_comments"
                ]
            ]
        );

        return $defineFields[$case][$type] ?? [];
    }