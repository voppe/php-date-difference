<?php

namespace Voppe\Interfaces;

interface VoppeDateInterface {

    public function setStartDate($date);

    public function setEndDate($date);

    public function isValidDate($date);

    public function diff();
}
