<?php
namespace Modules\Api\Controllers;

use \Entity\Song;
use \Entity\SongHistory;
use \Entity\SongVote;

class SongController extends BaseController
{
    public function listAction()
    {
        $all_songs = Song::fetchArray();
        $export_data = array();

        foreach($all_songs as $song)
        {
            $export_data[$song['id']] = Song::api($song);
        }

        return $this->returnSuccess($export_data);
    }

    public function indexAction()
    {
        if (!$this->hasParam('id'))
        {
            $this->dispatcher->forward(array(
                'controller' => 'song',
                'action' => 'list',
            ));
            return false;
        }

        $id = $this->getParam('id');

        $record = Song::find($id);

        if (!($record instanceof Song))
            return $this->returnError('Song not found.');

        $return = $record->toArray();

        // Handle display of external data.
        foreach($return as $r_key => $r_val)
        {
            if (substr($r_key, 0, 8) == 'external')
                unset($return[$r_key]);
        }
        $return['external'] = $record->getExternal();

        return $this->returnSuccess($return);
    }

    /**
     * Voting Functions
     */

    public function likeAction()
    {
        return $this->_vote(1);
    }
    public function dislikeAction()
    {
        return $this->_vote(0-1);
    }
    public function clearvoteAction()
    {
        return $this->_vote(0);
    }

    protected function _vote($value)
    {
        $sh_id = (int)$this->_getParam('sh_id');
        $sh = SongHistory::find($sh_id);

        if ($sh instanceof SongHistory)
        {
            if ($value == 0)
                $vote_result = $sh->clearVote();
            else
                $vote_result = $sh->vote($value);

            if ($vote_result)
                return $this->returnSuccess('OK');
            else
                return $this->returnError('Vote could not be applied.');
        }
        else
        {
            return $this->returnError('Song history record not found.');
        }
    }
}