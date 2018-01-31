<?php

namespace V1\Routes;

class Signup extends \Base\Rest
{
    function init($app, $params)
    {
        \V1\Models\Users::setup();
    }

    function post($app, $params)
    {
        $rules = [
            'email'    => 'required|valid_email|dbunique,\V1\Models\Users',
            'username' => 'required|min_len,3|max_len,25|alpha_dash|dbunique,\V1\Models\Users',
            'password' => 'required|min_len,6|max_len,25',
            'confirm'  => 'required|equalsfield,password',
        ];
        $valid = \Validate::is_valid($this->request->post(), $rules);
        if ($valid === true) {
            $data = $this->request->post();
            $data['uid'] = true;
            $data['rights'] = ['member'];
            $user = new \V1\Models\Users();
            $user->copyfrom($user->clean($data));
            $user->touch('created');
            if ($user->save()) {
                $data = $user->cast(); unset($data['password']);
                $this->response->set([
                    'status'  => 201,
                    'success' => true,
                    'message' => $app->get('LANG.V1.response.signup.success'),
                    'token'   => \Token::encode([
                        'uid'    => $data['uid'],
                        'rights' => $data['rights'],
                    ], $app->APPKEY),
                    'content' => $data
                ]);
            } else {
                $this->response->set([
                    'status'  => 400,
                    'message' => $app->get('LANG.V1.response.signup.failure')
                ]);
            }
        } else {
            $this->response->set('errors', $valid);
        }
    }
}