<?php
/**
 * Created by PhpStorm.
 * User: wade
 * Date: 16/5/4
 * Time: 上午11:11
 */
class PlayCards
{
    public $suits = array('s', 'h', 'd', 'p');
    public $figures = array('a','2', '3', '4', '5', '6', '7', '8', '9', '10', 'j', 'q', 'k');
    //1 单张 2对子 3 顺子 4 同花 5 同花顺 6 豹子 7 豹子A
    public $odds = array('7' =>188,'6'=>88,'5'=>50,'4'=>6,'3'=>4,'2'=>2,'1'=>1.2,'0'=>0);
    public $cards = array();
    public $figure = array();
    public $suit = array();
    public function __construct()
    {
        $cards = array();
        foreach($this->suits as $suit){
            foreach($this->figures as $figure){
                $cards[] = array($suit,$figure);
            }
        }
        $this->cards = $cards;
    }
    public function getCard()
    {
        shuffle($this->cards);
        //生成3张牌
        $return = array();
        $card =  array(array_pop($this->cards), array_pop($this->cards), array_pop($this->cards));
        foreach($card as $v){
            $data = $v[0].$v[1];
            $this->suit[] = $v[0];
            $this->figure[] = array_search($v[1],$this->figures)+2;
            $return[] = $data;
        }
        //补齐前导0
        for($i = 0; $i < 3; $i++){
            $this->figure[$i] = str_pad($this->figure[$i],2,'0',STR_PAD_LEFT);
        }
        rsort($this->figure);
        //对于对子做特殊处理
        if($this->figure[1] == $this->figure[2]){
            $temp = $this->figure[0];
            $this->figure[0] = $this->figure[2];
            $this->figure[2] = $temp;
        }
        return $return;
    }
    public function getFigure(){
        return $this->figure;
    }
    public function getSuit(){
        return $this->suit;
    }
    public function getOdds($type){
        return $this->odds[$type];
    }
    public function getType()
    {
        $figure = $this->getFigure();
        $suit = $this->getSuit();
        //豹子A
        if($figure[0] == '14' && $figure[1] == '14' && $figure[2]=='14'){
            return '7';
        }
        //豹子
        elseif($this->figure[0] == $figure[1] && $figure[0] == $figure[2]){
            return '6';
        }
        //同花顺
        elseif($suit[0] == $suit[1] && $suit[0] == $suit[2] &&($figure[0] == $figure[1]+1 && $figure[1] == $figure[2]+1 || implode($figure) =='140302')){
            return '5';
        }
        //同花
        elseif($suit[0] == $suit[1] && $suit[0] == $suit[2]){
            return '4';
        }

        //顺子
        elseif($figure[0] == $figure[1]+1 && $figure[1] == $figure[2]+1 || implode($figure) =='140302'){
            return '3';
        }
        //对子
        elseif($figure[0] == $figure[1] && $figure[1] != $figure[2]){
            return '2';
        }
        //K以上单张
        elseif($figure[0] >= '13'){
            return '1';
        }
        else{
         return '0';
        }
    }
    public function prob( $probs ){
        $step = 0;
        $rand = mt_rand(1, array_sum($probs));
        foreach ( $probs as $id => $prob )
        {
            if ( $step < $rand && $rand <= $prob + $step ) return $id;
            $step += $prob;
        }
        return 0;
    }
    public function getCards($type){
        $cards = array();
        if($type == '0'){
            $cards = $this->getNoRewardCards();
        }
        elseif($type == '1'){
            $cards = $this->getKUpCards();
        }
        elseif($type == '2'){
            $cards = $this->getPairCards();
        }
        elseif($type == '3'){
            $cards = $this->getStraightCards();
        }
        elseif($type == '4'){
            $cards = $this->getFlushCards();
        }
        elseif($type == '5'){
            $cards = $this->getFlushStraightCards();
        }
        elseif($type == '6'){
            $cards = $this->getThreeCards();
        }
        elseif($type == '7'){
            $cards = $this->getThreeACards();
        }
        return $cards;
    }
    //获得没中奖的卡
    public function getNoRewardCards(){
        shuffle($this->suits);
        $figures = array( '2','3', '4', '5', '6', '7', '8', '9', '10', 'j','q');
        $rand_keys = array_rand($figures, 2);
        $figures1=$figures;
        unset($figures[$rand_keys[0]]);
        unset($figures[$rand_keys[1]]);
        if($rand_keys[0] - $rand_keys[1] == 1 ){
            if($rand_keys[0] < 10){
                unset($figures[$rand_keys[0]+1]);
            }
            if($rand_keys[1] > 0){
                unset($figures[$rand_keys[1]-1]);
            }
        }
        elseif($rand_keys[1] - $rand_keys[0] == 1 ){
            if($rand_keys[1] < 12){
                unset($figures[$rand_keys[1]+1]);
            }
            if($rand_keys[0] > 0){
                unset($figures[$rand_keys[0]-1]);
            }
        }elseif($rand_keys[0] - $rand_keys[1] == 2 ){
            unset($figures[$rand_keys[0]-1]);
        }elseif($rand_keys[1] - $rand_keys[0] == 2 ){
            unset($figures[$rand_keys[1]-1]);
        }
        shuffle($figures);
        //生成3张牌
        $cards = array();
        $cards[0] = $this->suits[0].$figures1[$rand_keys[0]];
        $cards[1] = $this->suits[1].$figures1[$rand_keys[1]];
        $cards[2] = $this->suits[2].$figures[0];
        return $cards;
    }
    //获得K以上单张
    public function getKUpCards(){
        shuffle($this->suits);
        $figures = array( '3', '4', '5', '6', '7', '8', '9', '10', 'j');
        $figures1 = array('k', 'a');
        shuffle($figures1);
        shuffle($figures);
        //生成3张牌
        $cards = array();
        $cards[0] = $this->suits[0].$figures[0];
        $cards[1] = $this->suits[1].$figures[1];
        $cards[2] = $this->suits[2].$figures1[0];
        shuffle($cards);
        return $cards;

    }
    //获得对子
    public function getPairCards(){
        $figures = array();
        shuffle($this->suits);
        shuffle($this->figures);
        $figures[] = $this->figures[0];
        $figures[] = $this->figures[0];
        $figures[] = $this->figures[1];
        shuffle($figures);
        //生成3张牌
        $cards = array();
        $cards[0] = $this->suits[0].$figures[0];
        $cards[1] = $this->suits[1].$figures[1];
        $cards[2] = $this->suits[2].$figures[2];
        return $cards;

    }
    //获得顺子
    public function getStraightCards(){
        shuffle($this->suits);
        $figure = array();
        $rand = mt_rand(0,10);
        $figure[] = $this->figures[$rand];
        $figure[] = $this->figures[$rand + 1];
        $figure[] = $this->figures[$rand +2];
        shuffle($figure);
        //生成3张牌
        $cards = array();
        $cards[0] = $this->suits[0].$figure[0];
        $cards[1] = $this->suits[1].$figure[1];
        $cards[2] = $this->suits[2].$figure[2];
        shuffle($cards);
        return $cards;
    }
    //获得同花
    public function getFlushCards(){
        shuffle($this->suits);
        $rand_keys = array_rand($this->figures, 2);
        $figures=$this->figures;
        unset($figures[$rand_keys[0]]);
        unset($figures[$rand_keys[1]]);
        if($rand_keys[0] - $rand_keys[1] == 1 ){
            if($rand_keys[0] < 12){
                unset($figures[$rand_keys[0]+1]);
            }
            if($rand_keys[1] > 0){
                unset($figures[$rand_keys[1]-1]);
            }
        }
        elseif($rand_keys[1] - $rand_keys[0] == 1 ){
            if($rand_keys[1] < 12){
                unset($figures[$rand_keys[1]+1]);
            }
            if($rand_keys[0] > 0){
                unset($figures[$rand_keys[0]-1]);
            }
        }elseif($rand_keys[0] - $rand_keys[1] == 2 ){
            unset($figures[$rand_keys[0]-1]);
        }elseif($rand_keys[1] - $rand_keys[0] == 2 ){
            unset($figures[$rand_keys[1]-1]);
        }
        shuffle($figures);
        //生成3张牌
        $cards = array();
        $cards[0] = $this->suits[0].$this->figures[$rand_keys[0]];
        $cards[1] = $this->suits[0].$this->figures[$rand_keys[1]];
        $cards[2] = $this->suits[0].$figures[0];
        return $cards;

    }
    //获得同花顺
    public function getFlushStraightCards(){
        shuffle($this->suits);
        $figure = array();
        $rand = mt_rand(0,10);
        $figure[] = $this->figures[$rand];
        $figure[] = $this->figures[$rand + 1];
        $figure[] = $this->figures[$rand +2];
        shuffle($figure);
        //生成3张牌
        $cards = array();
        $cards[0] = $this->suits[0].$figure[0];
        $cards[1] = $this->suits[0].$figure[1];
        $cards[2] = $this->suits[0].$figure[2];
        shuffle($cards);
        return $cards;

    }
    //获得豹子
    public function getThreeCards(){
        shuffle($this->suits);
        $figures = array_pop($this->figures);
        shuffle($figures);
        //生成3张牌
        $cards = array();
        $cards[0] = $this->suits[0].$figures[0];
        $cards[1] = $this->suits[1].$figures[0];
        $cards[2] = $this->suits[2].$figures[0];
        return $cards;
    }
    //获得A豹子
    public function getThreeACards(){
        shuffle($this->suits);
        //生成3张牌
        $cards = array();
        $cards[0] = $this->suits[0].'a';
        $cards[1] = $this->suits[1].'a';
        $cards[2] = $this->suits[2].'a';
        return $cards;
    }
}
