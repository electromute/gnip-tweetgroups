-- phpMyAdmin SQL Dump
-- version 2.10.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 03, 2009 at 01:05 PM
-- Server version: 5.0.41
-- PHP Version: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `actorRules`
-- 

CREATE TABLE `actorRules` (
  `username` varchar(100) NOT NULL,
  `picURL` varchar(255) default NULL,
  `dateadded` datetime NOT NULL,
  `active` int(1) NOT NULL default '1',
  `admin` int(1) NOT NULL default '0',
  `lastupdated` datetime NOT NULL,
  PRIMARY KEY  (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `admin`
-- 

CREATE TABLE `admin` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(150) NOT NULL,
  `password` varchar(150) default NULL,
  `lastlogin` datetime NOT NULL,
  `activationcode` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `rawxml`
-- 

CREATE TABLE `rawxml` (
  `id` int(11) NOT NULL auto_increment,
  `raw` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `tweets`
-- 

CREATE TABLE `tweets` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(100) NOT NULL,
  `tweet` varchar(255) NOT NULL,
  `URL` varchar(255) default NULL,
  `time` datetime NOT NULL,
  `client` varchar(100) NOT NULL,
  `replyto` varchar(100) default NULL,
  `timeadded` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
