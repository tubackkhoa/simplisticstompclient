# Simplistic STOMP Client for PHP #

This is an extremely simple (simplistic?) client for Streaming Text Orientated Messaging Protocol (STOMP), written in PHP. It has no intentions of replacing the already existing STOMP client for PHP (http://stomp.codehaus.org/PHP).

## The reasons for making it was ##
  * I needed code with as little complexity as possible to lower risk of bugs and to have maximum performance for our specific usage.
  * The existing client did not work with RabbitMQ's STOMP gateway (http://www.rabbitmq.com/).
  * The existing client had a tendency of getting stuck in loops when things failed.

## Scope ##
  * It is only tested with the RabbitMQ STOMP gateway and RabbitMQ Server 1.4.0 (http://www.rabbitmq.com/).
  * It only supports sending messages to STOMP, no support for sending other commands than 'CONNECT', 'SEND', 'DISCONNECT', receiving commands 'CONNECTED', 'RECEIPT'.
  * It assumes the length of 

&lt;session-id&gt;

 to be the same on all systems, I do not know if this is a fair assumption.

## Facts ##
  * Currently used in production at [Heysan](http://www.heysan.com) to add some of our http requests to a queue instead of letting PHP do the heavy, and synchronous, work of doing the http requests to [Admob](http://www.admob.com).

## Contribute ##
  * I'd be happy to add Project Members to this project, any improvements are welcome. Feel free to tell me if you use this client, always fun to know. Contact me. I will not contribute anything to this project in a foreseeable future.

## Contact ##
Jonatan Kallus

jonatan.kallus@gmail.com