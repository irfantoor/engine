<?php

namespace IrfanTOOR\Engine;

use App\Model\Sessions;
use IrfanTOOR\Collection;
use IrfanTOOR\Debug;
use IrfanTOOR\Engine\Http\Cookie;

class Session extends Collection
{
    protected $sessions;
    protected $created_at;
    protected $updated_at;

    function __construct($request)
    {
        $server  = $request->get('server');
        $sid     = $request->get('cookie')['SID'];

        $this->sessions = new Sessions;

        if (!$sid)
        {
            $sid = md5($server['REMOTE_ADDR'] . $server['HTTP_USER_AGENT'] . time());
            $c = new Cookie([
                'name'  => 'SID',
                'value' => $sid,
            ]);
            $c->send();

            $this->sessions->insertOrUpdate(
                [
                    'sid' => $sid,
                    'value' => json_encode([]),
                    'updated_at' => time(),
                ]
            );
        }

        $session = $this->sessions->getFirst(
            [
                'where' => 'sid = :sid'
            ],
            [
                'sid' => $sid,
            ]
        );

        $value['sid'] = $sid;
        foreach(json_decode($session['value'], 1) as $k=>$v) {
                $value[$k] = $v;
        }

        $this->created_at = $session['created_at'];
        $this->updated_at = $session['updated_at'];
        parent::__construct($value);

        # 10 minutes of inactivity will remove the token logged
        if ((time() - $this->updated_at) > 10 * 60)
            $this->remove('logged');
    }

    function set($id, $value = null)
    {
        parent::set($id, $value);
        $this->save();
    }

    function remove($id)
    {
        parent::remove($id);
        $this->save();
    }

    function destroy()
    {
        $sid = $this->get('sid');
        $this->sessions->remove(
            [
                'where' => 'sid = :sid'
            ],
            [
                'sid' => $sid,
            ]
        );
    }

    function save()
    {
        $this->sessions->insertOrUpdate(
            [
                'sid' => $this->get('sid'),
                'value' => json_encode($this->toArray()),
                'updated_at' => time(),
            ]
        );
    }
}
