<?php

namespace IrfanTOOR\Engine;

use App\Model\Sessions;
use IrfanTOOR\Collection;
use IrfanTOOR\Debug;
use IrfanTOOR\Engine\Http\Cookie;

class Session extends Collection
{
    protected $id;
    protected $sid;
    protected $created_at;
    protected $updated_at;    
    protected $sessions;

    function __construct($request)
    {
        $this->sessions = new Sessions;
        register_shutdown_function([$this, 'save']);
        $server  = $request->get('server');
        $sid     = $request->get('cookie.sid', null);
        
        # verify integrity of sid
        if ($sid) {
            preg_match('|([0-9a-f]{32})|', $sid, $m);
            if (!isset($m[1]) || ($m[1] !== $m[0])) {
                $sid = null;
            }
        }
        
        if (!$sid) {
            $sid = md5(
                'IrfanTOOR\\Engine\\Session' .
                $server['HTTP_HOST'] .
                $server['REMOTE_ADDR'] . 
                $server['HTTP_USER_AGENT']
            );
            
            # send cookie
            $c = new Cookie([
                'name'  => 'sid',
                'value' => $sid,
            ]);
            
            $c->send();            
        }
        
        $session = $this->getSession($sid);
        if (!$session) {
            # create session
            $this->sessions->insert(
                [
                    'sid'        => $sid,
                    'value'      => '[]',                   # json_encode([])
                    'updated_at' => $server['REQUEST_TIME'] # time(),
                ]
            );
            
            $session = $this->getSession($sid);
        }
        
        $v = json_decode($session['value'], true);
        $this->set($v);
        
        foreach($session as $k=>$v) {
            if (is_int($k))
                continue;
                
            if ($k === 'value')
                continue;
                
            $this->$k = $v;
        }
                
        # 10 minutes of inactivity will remove the token logged
        if ((time() - $session['updated_at']) > 10 * 60) {
            $this->destroy();
        }
        
        $this->updated_at = $server['REQUEST_TIME'];
    }
    
    function getSession($sid) {    
        return $this->sessions->getFirst(
            ['where' => 'sid = :sid'],
            ['sid' => $sid]
        );
    }
    
    function destroy()
    {
        foreach ($this->toArray() as $k=>$v) {
            $this->remove($k);
        }
        
        $this->save();
    }

    function save()
    {
        if ($this->sid) {
            $this->sessions->insertOrUpdate(
                [
                    'id'         => $this->id,
                    'sid'        => $this->sid,
                    'value'      => json_encode($this->toArray()),
                    'created_at' => $this->created_at,
                    'updated_at' => $this->updated_at,
                ]
            );
        }
    }
}
