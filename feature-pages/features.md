---
title: Features
layout: default
---

# {{ page.title }}

ROS is a large ecosystem of packages which provide different types of functionality and capabilities at all different quality and maturity levels. To better understand whether or not ROS will be useful to you, this page covers much of the core value which ROS provides.

ROS stands for the Robot Operating System, but in reality ROS is a meta operating system of sorts. ROS builds on one of several existing operating systems, like Ubuntu Linux, and provides operating system like facilities to roboticists. For example, ROS provides a way to interact with other parts of the robot system, tools for developing parts of the system, and infrastructure for releasing, testing, deploying, and documenting open source parts of the system.

# Core ROS Features

While we cannot give an exhaustive list of what is in the ROS ecosystem we can identify some of the core parts of ROS and talk about their functionality, technical specifications, and quality in order to give you a better idea of what ROS can provide to your project.

## Communications Infrastructure

At the lowest level ROS provides a message passing interface which provides [inter-process communication](http://en.wikipedia.org/wiki/Inter-process_communication) and is commonly referred to as a [middleware](http://en.wikipedia.org/wiki/Middleware).

The ROS middleware provides these facilities:

- [publish/subscribe anonymous message passing](http://wiki.ros.org/Topics)
- [message passing IDL](http://wiki.ros.org/Messages) for defining data types
- [recording and playback of messages](http://wiki.ros.org/rosbag)
- [request/response remote procedure calls](http://wiki.ros.org/Services)
- [distributed parameter system](http://wiki.ros.org/Parameter%20Server)

### Message Passing

Robots tend consist of many separate tasks being performed in parallel, e.g. reading data from a sensor, transforming data from sensors, interpreting data from sensors, making decisions based on data analysis, and executing actions. Many of these tasks lend themselves to many decoupled tasks working together asynchronously. This is often implemented as many independent processes on a computer, and therefore there arises a need for a mechanism over which these disparate processes can communicate. Therefore, it isn't surprising that with ROS the majority of communication between processes takes place using the [anonymous publish/subscribe](http://wiki.ros.org/Topics) mechanism over named topics. The structure of the data being passed is defined in the [message IDL](http://wiki.ros.org/Messages).

### Recording and Playback of Messages

Since the [publish/subscribe system](http://wiki.ros.org/Topics) is anonymous and asynchronous, the data can easily be captured and replayed without any changes to the code of participating processes. For example, if you have a task which reads data from a sensor, and you wish to develop on a task which does some processing to that task, ROS makes it easy to capture the data published from the sensor task to a file, and then republish that data in the file off-line at a later time. The interfaces between tasks makes it such that the task doing processing on the data can be agnostic to the source of the data. This is a powerful design pattern that can significantly reduce your development effort and promote flexibility and modularity in your own system.

### Remote Procedure Calls

The asynchronous nature of publish/subscribe works for many design patterns in robotics, but sometimes you need to have synchronous, request and response style communication between processes. The ROS middleware provides this using its [Services](http://wiki.ros.org/Services) mechanism. Like [Topics](http://wiki.ros.org/Topics), the data being sent between processes in a Service call are defined with the same simple [message IDL](http://wiki.ros.org/Messages).

### Distributed Parameter System

The ROS middleware also provides a way for disparate tasks to share configurations, by providing a global key value store, allowing you to easily modify the settings of your tasks, and even allow tasks to change the configuration of each other.

### Middleware Quality and Robustness

The ROS middleware and core facilities were developed originally in 2008, and have been used in a variety of applications, domains, and robots since.

TODO: Insert code quality metrics

### Development Tools

Along side the middleware are development tools which aid in software development process by providing introspection into the middleware, debugging tools, build system tools, and other tools which have arisen out of community conventions. The anonymous and asynchronous publish/subscribe mechanism allows you to introspect data moving around the system spontaneously, which makes it much easier to debug and comprehend issues when they occur. The developer tools make this even easier by providing simple, easy to use command line tools for doing this introspection.

## Robot Specific Features

In addition to the core middleware components, ROS provides common robot specific tools and frameworks in order to get your robot up and running faster. This is a just a few of the robot specific capabilities ROS can provide:

- Standard Message Definitions for Robots ([common_msgs](http://wiki.ros.org/common_msgs))
- Robot Geometry Library ([tf](http://wiki.ros.org/tf))
- Robot Description ([robot_model](http://wiki.ros.org/robot_model))
- Preemptable Remote Procedure Calls ([actionlib](http://wiki.ros.org/actionlib))
- Diagnostics ([diagnostics](http://wiki.ros.org/diagnostics))
- Pose Estimation ([robot_pose_ekf](http://wiki.ros.org/robot_pose_ekf))
- Localization ([amcl](http://wiki.ros.org/amcl))
- Mapping ([gmapping](http://wiki.ros.org/gmapping))
- Navigation ([navigation](http://wiki.ros.org/navigation))

### Standardized Robot Messages

While the middleware provides a message IDL so that you can easily create your own message definitions, there exists a set of community selected messages which cover the eighty percent of cases for robotics. These messages cover geometry like pose and orientation, kinematics and dynamics like Twists and Wrenches, and sensors like lasers or cameras. using these messages allows you to focus on your robot and not how to standardize messages, and also increases interoperability with existing tools and capabilities in the ROS community.

### Robot Geometry Library

<img src="{{ site.baseurl }}/img/tf.png" style="float: right; width: 400px;" alt="tf - Transforms Library" />

One of the problems which comes up almost immediately in most robotics projects, is the need to manage the robot's static and dynamic geometry. Whether you need to transform a laser scan from the sensors frame of reference to a global frame of reference or you need to get the location of the robot's end effector in the robot's local frame, the [tf](http://wiki.ros.org/tf) library can help you do that.

[tf](http://wiki.ros.org/tf) has been used to manage robots with more than one hundred degrees of freedom and remain responsive at hundreds of Hz of updates. [tf](http://wiki.ros.org/tf) allows you to define many static transforms, like a sensor statically mounted to a mobile base, and dynamic transforms, like each joint in a robotic arm. Once defined, you can update the dynamic joints and query for transform information given the current state or some state in the recent past. [tf](http://wiki.ros.org/tf) also handles the fact the the producers and consumers of this information are often distributed across processes or even computers.

### Robot Description

Another common robot problem ROS solves for you is how to describe your robot in a machine readable way. ROS provides the [robot_model](http://wiki.ros.org/robot_model) stack, which contains tools for describing and modeling your robot so that it can be used by tools like [tf](http://wiki.ros.org/tf), [robot_state_publisher](http://wiki.ros.org/robot_state_publisher), or [rviz](http://wiki.ros.org/rviz). The format for describing your robot in ROS is [urdf](http://wiki.ros.org/urdf), which is an XML document where you can define static and dynamic transforms as well as describe the visual and collision geometry of your robot.

Once defined you robot can be more easily used with the [tf](http://wiki.ros.org/tf) geometry library, rendered in three dimensions for nice visualizations, or even used with simulators or motion planners.

### Preemtable Remote Procedure Calls

While Topics (anonymous publish/subscribe) and Services (remote procedure calls) cover most of the communication use cases in robotics, sometimes you need to perform an action, monitor its progress, and preempt it if conditions change. ROS provides a mechanism to do just this in the [actionlib](http://wiki.ros.org/actionlib) library. Actions are just like Service calls except they can report progress before returning the final response and they can be preempted by the caller. This allows you to, for example, instruct your robot to navigate to a given set of coordinates, while you monitor its progress and the environment, and preempt that instruction if conditions change. This is a powerful concept which is used throughout the ROS ecosystem, and can be brought to bare in your robot too.

### Diagnostics

ROS provides a standard way to produces, collection, and aggregate diagnostics about your robot, so that at a glance you can quickly see the state of your robot and how you can address issues when they happen.

### Pose Estimation, Localization, and Navigation

ROS also provides some "batteries included" functionality specific to robotics to help you get started on your robotics project. There exists ROS packages for solving basic robotics tasks like [pose estimation](http://wiki.ros.org/robot_pose_ekf), [localization](http://wiki.ros.org/amcl), [SLAM](http://wiki.ros.org/gmapping), or even mobile [navigation](http://wiki.ros.org/navigation).

Whether you are an engineer looking to do some rapid research and development, a robotics researcher wanting to get your research sooner than later, or a hobbyist looking to learn more about robotics, these out of the box capabilities should help you do more with less effort.

## Tools

Robot systems are often complex and sophisticated systems, but that doesn't mean that they have to be complicated or intractable. ROS provides amazing tools to help you manage the complexity, understand what is going on with your robot, and introspect your robot at any point and any time.

### Rviz

<img src="{{ site.baseurl }}/img/rviz.png" style="float: left; width: 400px; padding-right: 10px; padding-bottom: 10px;" alt="rviz" />

Probably the most well know tool in ROS, [rviz](http://wiki.ros.org/rviz) provides general purpose three dimensional visualization of standard sensor data types as well as robots described with the [urdf](http://wiki.ros.org/urdf) format.

[rviz](http://wiki.ros.org/rviz) can visualize many of the common message types provided in ROS, such as laser scans, three dimensional point clouds, and camera images. It also uses the information from the [tf](http://wiki.ros.org/tf) geometry library in order to show all of the sensor data in a common coordinate frame, along side a three dimensional rendering of your robot, as long as you described it in a [urdf](http://wiki.ros.org/urdf) file. Having all of your data visualized in the same coordinate frame not only looks amazing, but also allows you to quickly see what your robot sees, and possibly identify places where your sensors are misaligned or your robot description is inaccurate.

### rQt

<img src="{{ site.baseurl }}/img/rqt.png" style="float: right; width: 400px; padding-right: 10px; padding-bottom: 10px;" alt="rqt" />

ROS provides a Qt based framework for developing dashboards for you robot called [rqt](http://wiki.ros.org/rqt). You can do this by organizing existing [rqt](http://wiki.ros.org/rqt) plugins as well as your own Qt/ROS plugins into tabs and split screen layouts.

TODO: Talk about each of the tools

rqt_graph

rqt_deps

rqt_plot

rqt topic introspection

etc...

more

# Integration with Other Libraries

TODO

## Gazebo

TODO

## OpenCV

TODO

## PCL

TODO

## MoveIt!

TODO

# Community Packages

TODO


