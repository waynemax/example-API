<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Video\V1\Room;

use Twilio\Options;
use Twilio\Values;

abstract class RoomParticipantOptions {
    /**
     * @param string $status The status
     * @param string $identity The identity
     * @param \DateTime $dateCreatedAfter The date_created_after
     * @param \DateTime $dateCreatedBefore The date_created_before
     * @return ReadRoomParticipantOptions Options builder
     */
    public static function read($status = Values::NONE, $identity = Values::NONE, $dateCreatedAfter = Values::NONE, $dateCreatedBefore = Values::NONE) {
        return new ReadRoomParticipantOptions($status, $identity, $dateCreatedAfter, $dateCreatedBefore);
    }

    /**
     * @param string $status The status
     * @return UpdateRoomParticipantOptions Options builder
     */
    public static function update($status = Values::NONE) {
        return new UpdateRoomParticipantOptions($status);
    }
}

class ReadRoomParticipantOptions extends Options {
    /**
     * @param string $status The status
     * @param string $identity The identity
     * @param \DateTime $dateCreatedAfter The date_created_after
     * @param \DateTime $dateCreatedBefore The date_created_before
     */
    public function __construct($status = Values::NONE, $identity = Values::NONE, $dateCreatedAfter = Values::NONE, $dateCreatedBefore = Values::NONE) {
        $this->options['status'] = $status;
        $this->options['identity'] = $identity;
        $this->options['dateCreatedAfter'] = $dateCreatedAfter;
        $this->options['dateCreatedBefore'] = $dateCreatedBefore;
    }

    /**
     * The status
     * 
     * @param string $status The status
     * @return $this Fluent Builder
     */
    public function setStatus($status) {
        $this->options['status'] = $status;
        return $this;
    }

    /**
     * The identity
     * 
     * @param string $identity The identity
     * @return $this Fluent Builder
     */
    public function setIdentity($identity) {
        $this->options['identity'] = $identity;
        return $this;
    }

    /**
     * The date_created_after
     * 
     * @param \DateTime $dateCreatedAfter The date_created_after
     * @return $this Fluent Builder
     */
    public function setDateCreatedAfter($dateCreatedAfter) {
        $this->options['dateCreatedAfter'] = $dateCreatedAfter;
        return $this;
    }

    /**
     * The date_created_before
     * 
     * @param \DateTime $dateCreatedBefore The date_created_before
     * @return $this Fluent Builder
     */
    public function setDateCreatedBefore($dateCreatedBefore) {
        $this->options['dateCreatedBefore'] = $dateCreatedBefore;
        return $this;
    }

    /**
     * Provide a friendly representation
     * 
     * @return string Machine friendly representation
     */
    public function __toString() {
        $options = array();
        foreach ($this->options as $key => $value) {
            if ($value != Values::NONE) {
                $options[] = "$key=$value";
            }
        }
        return '[Twilio.Video.V1.ReadRoomParticipantOptions ' . implode(' ', $options) . ']';
    }
}

class UpdateRoomParticipantOptions extends Options {
    /**
     * @param string $status The status
     */
    public function __construct($status = Values::NONE) {
        $this->options['status'] = $status;
    }

    /**
     * The status
     * 
     * @param string $status The status
     * @return $this Fluent Builder
     */
    public function setStatus($status) {
        $this->options['status'] = $status;
        return $this;
    }

    /**
     * Provide a friendly representation
     * 
     * @return string Machine friendly representation
     */
    public function __toString() {
        $options = array();
        foreach ($this->options as $key => $value) {
            if ($value != Values::NONE) {
                $options[] = "$key=$value";
            }
        }
        return '[Twilio.Video.V1.UpdateRoomParticipantOptions ' . implode(' ', $options) . ']';
    }
}