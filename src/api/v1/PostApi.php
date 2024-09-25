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

            register_rest_route('api/v1', '/posts/(?P<id>\d+)', [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'post'],
                'args' => [
                    'id' => [
                        'required' => true,
                        'validate_callback' => fn($param, $request, $key) => is_numeric($param)
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

            return rest_ensure_response(compact('posts'));
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
            $postId = (int) $request['id'];
            $post = get_post($postId);

            if (empty($post) || $post->post_status !== 'publish')
                throw new \Exception('Post not found', 404);

            // prepare response
            $response = [
                'id' => $post->ID,
                'title' => get_the_title($post),
                'content' => apply_filters('the_content', $post->post_content),
                'date' => get_the_date('', $post),
                'excerpt' => get_the_excerpt($post),
                'author' => get_the_author($post),
            ];

            return rest_ensure_response($response);
        } catch (\Throwable $th) {
            return rest_ensure_response(new \WP_Error($th->getCode(), $th->getMessage()));
        }
    }

}