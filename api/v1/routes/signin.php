<?php

namespace V1\Routes;

class Signin extends \Base\Rest
{
    function init($app, $params)
    {
        \V1\Models\Users::setup();
    }

    function post($app, $params)
    {
        $rules = [];
        $rules['password'] = 'required';
        if (!$this->request->post('email') && !$this->request->post('username')) {
            $rules['email'] = 'required';
        }
        if (!$this->request->post('username') && !$this->request->post('email')) {
            $rules['username'] = 'required';
        }
        $valid = \Validate::is_valid($this->request->post(), $rules);
        if ($valid === true) {
            $user = new \V1\Models\Users();
            $user->load(['email=? OR username=?', $this->request->post('email'), $this->request->post('username')]);
            if ($user->valid() && password_verify($this->request->post('password'), $user->password)) {
                $data = $user->cast(); unset($data['password']);
                $this->response->set([
                    'status'  => 200,
                    'success' => true,
                    'message' => $app->get('LANG.V1.response.signin.success'),
                    'token'   => \Token::encode([
                        'uid'    => $user->uid,
                        'rights' => $user->rights,
                    ], $app->APPKEY),
                    'content' => $data
                ]);
            } else {
                $this->response->set('message', $app->get('LANG.V1.response.signin.failure'));
            }
        } else {
            $this->response->set('errors', $valid);
        }
    }
}