<?php

namespace Src\api\v1;
use Src\traits\CheckRequestOrigin;


class PostApi
{
    use CheckRequestOrigin;
    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route('api/v1', '/posts', [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'posts'],
            ]);

            register_rest_route('api/v1', '/posts/(?P<slug>[a-zA-Z0-9-_]+)', [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'post'],
                'args' => [
                    'slug' => [
                        'required' => true,
                        'validate_callback' => fn($param, $request, $key) => is_string($param)
                    ]
                ],
            ]);
        });

    }


    /**
     * 
     * @return \WP_REST_Response|\WP_Error 
     */
    public function posts()
    {
        try {
            if (!$this->isOriginValid())
                throw new \Exception('Request origin is invalid', 403);

            $posts = get_posts([
                'numberposts' => -1,
            ]);

            $posts = $this->postResource($posts);

            return rest_ensure_response($posts);
        } catch (\Throwable $th) {
            return rest_ensure_response(new \WP_Error($th->getCode(), $th->getMessage()));
        }
    }


    public function post($request)
    {
        try {
            if (!$this->isOriginValid())
                throw new \Exception('Request origin is invalid', 403);

            // get post 
            $slug = sanitize_text_field($request['slug']);
            //$post = get_page_by_path($slug, OBJECT, 'post');
            $posts = get_posts([
                'name' => $slug,
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => 1
            ]);
            if (empty($posts[0]) || $posts[0]->post_status !== 'publish')
                throw new \Exception('Post not found', 404);

            $post = $posts[0];

            // prepare response
            $post = $this->postResource($post);

            return rest_ensure_response($post);
        } catch (\Throwable $th) {
            return rest_ensure_response($th->getMessage());
        }
    }

    public function postResource(array|\WP_Post $data): array
    {
        // data is collection of posts
        if (is_array($data)) {
            return array_map(
                fn($post) => [
                    'slug' => $post->post_name,
                    'title' => $post->post_title,
                    'desc' => $post->post_content,
                    'excerpt' => get_the_excerpt($post),
                    'thumbnail' => get_the_post_thumbnail_url($post->ID, 'rectangle'),
                    'date' => get_the_date('', $post),
                ]
                ,
                $data
            );
        }

        // data is a single post
        return [
            'title' => get_the_title($data),
            'desc' => apply_filters('the_content', $data->post_content),
            'excerpt' => get_the_excerpt($data),
            'thumbnail' => get_the_post_thumbnail_url($data->ID, 'square'),
            'date' => get_the_date('', $data),
        ];
    }
}