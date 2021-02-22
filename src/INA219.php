<?php
/*
 * BSD 3-Clause License
 *
 * Copyright (c) 2019, TASoft Applications
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 *  Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 *  Neither the name of the copyright holder nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

namespace Ikarus\SPS\Raspberry\Adafruit;

use TASoft\Bus\I2C;

class INA219
{
	const INA219_REG_CONFIG				= 0x00;
	const INA219_REG_SHUNTVOLTAGE		= 0x01;
	const INA219_REG_BUSVOLTAGE			= 0x02;
	const INA219_REG_POWER				= 0x03;
	const INA219_REG_CURRENT			= 0x04;
	const INA219_REG_CALIBRATION		= 0x05;

	/** @var I2C */
	private $i2c;

	/**
	 * INA219 constructor.
	 * @param I2C $i2c
	 */
	public function __construct(I2C $i2c)
	{
		$this->i2c = $i2c;
		$i2c->write16(self::INA219_REG_CALIBRATION, 4096);

		$cfg = 0x2000;
		$cfg |= 0x0180;
		$cfg |= 0x0480;
		$cfg |= 0x0018;
		$cfg |= 0x0048;
		$cfg |= 0x07;

		$i2c->write16(self::INA219_REG_CONFIG, $cfg);
	}

	public function readBusVoltage() {
		$this->i2c->write16(self::INA219_REG_CALIBRATION, 4096);
		$cfg = 0x2000;
		$cfg |=0x1800;
		$cfg |= 0x0180;
		$cfg |= 0x0018;
		$cfg |= 0x0048;
		$cfg |= 0x07;
		$this->i2c->write16(self::INA219_REG_CONFIG, $cfg);
		usleep(600);

		$this->i2c->writeRegister(self::INA219_REG_BUSVOLTAGE);
		return (($this->i2c->read2Bytes()>>3)*4) * 0.001 + 0.2;
	}

	public function readShuntVoltage() {
		$this->i2c->write16(self::INA219_REG_CALIBRATION, 4096);
		$cfg = 0x2000;
		$cfg |=0x1800;
		$cfg |= 0x0180;
		$cfg |= 0x0018;
		$cfg |= 0x0048;
		$cfg |= 0x07;
		$this->i2c->write16(self::INA219_REG_CONFIG, $cfg);
		usleep(600);

		$this->i2c->writeRegister(self::INA219_REG_SHUNTVOLTAGE);
		return $this->i2c::convertToSignedInteger( $this->i2c->read2Bytes()>>3, 12 ) * 0.01;
	}

	public function readCurrent() {
		$this->i2c->write16(self::INA219_REG_CALIBRATION, 4096);
		$cfg = 0x2000;
		$cfg |=0x1800;
		$cfg |= 0x0180;
		$cfg |= 0x0018;
		$cfg |= 0x0048;
		$cfg |= 0x07;
		$this->i2c->write16(self::INA219_REG_CONFIG, $cfg);
		usleep(600);
		$this->i2c->writeRegister(self::INA219_REG_CURRENT);
		return $this->i2c::convertToSignedInteger( $this->i2c->read2Bytes()>>3, 12 );
	}
}