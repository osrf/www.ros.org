---
title: Features
layout: default
---

# {{ page.title }}

ROS is a large ecosystem of packages that provides different types of functionality and capabilities at varying quality and maturity levels. To better understand whether or not ROS will be useful to you, this page covers much of what ROS has to offer. 

ROS stands for the Robot Operating System, but in reality, ROS is a meta operating system of sorts. ROS builds on one of several existing operating systems, like Ubuntu Linux, and provides operating system-like facilities to roboticists. For example, ROS provides a way to interact with parts of a robot system, tools for developing the system, and infrastructure for releasing, testing, deploying, and documenting the system.

# Core ROS Features

While we cannot provide an exhaustive list of what is in the ROS ecosystem, we can identify some of the core parts of ROS and talk about their functionality, technical specifications, and quality in order to give you a better idea of what ROS can contribute to your project.

## Communications Infrastructure

At the lowest level, ROS offers a message passing interface that provides [inter-process communication](http://en.wikipedia.org/wiki/Inter-process_communication) and is commonly referred to as a [middleware](http://en.wikipedia.org/wiki/Middleware).

The ROS middleware provides these facilities:

- [publish/subscribe anonymous message passing](http://wiki.ros.org/Topics)
- [message passing IDL](http://wiki.ros.org/Messages) for defining data types
- [recording and playback of messages](http://wiki.ros.org/rosbag)
- [request/response remote procedure calls](http://wiki.ros.org/Services)
- [distributed parameter system](http://wiki.ros.org/Parameter%20Server)

### Message Passing

A communication system is often one of the first needs to arise when implementing a new robot application. ROS's built-in and well-tested messaging system saves you time by managing the details of communication between distributed nodes via the [anonymous publish/subscribe](http://wiki.ros.org/Topics) mechanism. Another added benefit of using a message passing system is that it forces you to implement clear interfaces between the nodes in your system, thereby improving encapsulation and promoting code reuse. The structure of these message interfaces is defined in the [message IDL](http://wiki.ros.org/Messages).

### Recording and Playback of Messages

Since the [publish/subscribe system](http://wiki.ros.org/Topics) is anonymous and asynchronous, the data can easily be captured and replayed without any changes to the code of participating processes. For example, if you have Task A that reads data from a sensor, and you wish to develop Task B that does some processing with Task A, ROS makes it easy to capture the data published from Task A to a file, and then republish that data in the file off-line at a later time. The interfaces between tasks makes it such that Task B can be agnostic to the source of the data (Task A). This is a powerful design pattern that can significantly reduce your development effort and promote flexibility and modularity in your own system.

### Remote Procedure Calls

The asynchronous nature of publish/subscribe works for many communication needs in robotics, but sometimes you need to have synchronous, request and response style communication between processes. The ROS middleware provides this using its [Services](http://wiki.ros.org/Services) mechanism. Like [Topics](http://wiki.ros.org/Topics), the data being sent between processes in a Service call are defined with the same simple [message IDL](http://wiki.ros.org/Messages).

### Distributed Parameter System

The ROS middleware also provides a way for disparate tasks to share configurations, by providing a global key value store. This allows you to easily modify your task settings, and even allow tasks to change the configuration of other tasks.

### Middleware Quality and Robustness

The ROS middleware and core facilities were originally developed in 2008, and have since been used in a variety of applications, domains, and robots.

<p style="color: red; font-size: 50px;">TODO: Insert code quality metrics</p>

### Development Tools

Alongside the middleware are development tools that support the software development process by providing introspection into the middleware, debugging tools, build system tools, and other tools which have arisen out of community conventions. The anonymous and asynchronous publish/subscribe mechanism allows you to spontaneously introspect data moving around the system, which makes it much easier to debug and comprehend issues when they occur. The developer tools make this even easier by providing simple, easy to use command line tools for introspection.

## Robot-Specific Features

In addition to the core middleware components, ROS provides common robot-specific tools and frameworks that get your robot up and running faster. These are just a few of the robot-specific capabilities ROS can provide:

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

While the middleware provides a message IDL so that you can easily create your own message definitions, there exists a set of community-selected messages which cover about eighty percent of message definitions for robotics. These messages cover geometries like pose and orientation, kinematics and dynamics like twists and wrenches, and sensors like lasers and cameras. By using these messages, you can focus on your robot and not on standardizing messages. These standardized messages also increase interoperability with existing tools and capabilities in the ROS ecosystem.

### Robot Geometry Library

<img src="{{ site.baseurl }}/img/tf.png" style="float: right; width: 400px;" alt="tf - Transforms Library" />

One of the problems that comes up almost immediately in most robotics projects, is the need to manage the robot's static and dynamic geometry. Whether you need to transform a laser scan from the sensor's frame of reference to a global frame of reference, or you need to get the location of the robot's end effector in the robot's local frame, the [tf](http://wiki.ros.org/tf) library can help you do that.

[tf](http://wiki.ros.org/tf) has been used to manage robots with more than one hundred degrees of freedom while remaining responsive at hundreds of Hz of updates. [tf](http://wiki.ros.org/tf) allows you to define many static transforms, like a sensor statically mounted to a mobile base, and dynamic transforms, like each joint in a robotic arm. Once defined, you can update the dynamic joints and query for transform information given the current or any recent state. [tf](http://wiki.ros.org/tf) also handles the fact that the producers and consumers of this information are often distributed across processes or even computers.

### Robot Description

Another common robot problem that ROS solves for you is how to describe your robot in a machine-readable way. ROS provides the [robot_model](http://wiki.ros.org/robot_model) stack, which contains tools for describing and modeling your robot so that it can be used by tools like [tf](http://wiki.ros.org/tf), [robot_state_publisher](http://wiki.ros.org/robot_state_publisher), or [rviz](http://wiki.ros.org/rviz). The format for describing your robot in ROS is [urdf](http://wiki.ros.org/urdf), which consists of an XML document where you can define static and dynamic transforms as well as describe the visual and collision geometries of your robot.

Once defined, your robot can be more easily used with the [tf](http://wiki.ros.org/tf) geometry library, rendered in three dimensions for nice visualizations, or even used with simulators and motion planners.

### Preemtable Remote Procedure Calls

While Topics (anonymous publish/subscribe) and Services (remote procedure calls) cover most of the communication use cases in robotics, sometimes you need to perform an action, monitor its progress, and preempt it if conditions change. ROS provides a mechanism to do just this in the [actionlib](http://wiki.ros.org/actionlib) library. Actions are just like Service calls except they can report progress before returning the final response, and they can be preempted by the caller. This allows you to, for example, instruct your robot to navigate to a given set of coordinates while you monitor its progress and the environment, and preempt that instruction if conditions change. This is a powerful concept which is used throughout the ROS ecosystem.

### Diagnostics

ROS provides a standard way to produce, collect, and aggregate diagnostics about your robot so that at a glance, you can quickly see the state of your robot and determine how to address issues as they arise.

### Pose Estimation, Localization, and Navigation

ROS also provides some "batteries included" functionality that helps you get started on your robotics project. There exist ROS packages for solving basic robotics tasks like [pose estimation](http://wiki.ros.org/robot_pose_ekf), [localization](http://wiki.ros.org/amcl), [SLAM](http://wiki.ros.org/gmapping), or even mobile [navigation](http://wiki.ros.org/navigation).

Whether you are an engineer looking to do some rapid research and development, a robotics researcher wanting to get your research done in a timely fashion, or a hobbyist looking to learn more about robotics, these out-of-the-box capabilities should help you do more, with less effort.

## Tools

Robot systems are often complex and sophisticated, but that doesn't mean that they have to be complicated or intractable. ROS provides amazing tools to help you manage this complexity by making it easier to understand the state of your robot.

### Command-Line Tools

Spend all of your time remotely logged into a robot? ROS can be used 100% without a GUI. All core functionality and introspection tools are accessible via one of the over 45 ROS command line tools. There are commands for launching groups of nodes, introspecting topics, services, and actions, recording and playing back data, and a host of other options. If you prefer using graphical tools, Rviz and rQt provide similar (and extended) functionality.

### Rviz

<img src="{{ site.baseurl }}/img/rviz.png" style="float: left; width: 400px; padding-right: 10px; padding-bottom: 10px;" alt="rviz" />

Perhaps the most well-known tool in ROS, [rviz](http://wiki.ros.org/rviz) provides general purpose three dimensional visualization of standard sensor data types as well as [urdf](http://wiki.ros.org/urdf)-described robots.

[rviz](http://wiki.ros.org/rviz) can visualize many of the common message types provided in ROS, such as laser scans, three dimensional point clouds, and camera images. It also uses the information from the [tf](http://wiki.ros.org/tf) geometry library in order to show all of the sensor data in a common coordinate frame, along side a three dimensional rendering of your robot, as long as you described it in a [urdf](http://wiki.ros.org/urdf) file. Having all of your data visualized in the same coordinate frame not only looks amazing, but also allows you to quickly see what your robot sees, and possibly identify places where your sensors are misaligned or your robot description is inaccurate.

### rQt

<img src="{{ site.baseurl }}/img/rqt.png" style="float: right; width: 400px; padding-right: 10px; padding-bottom: 10px;" alt="rqt" />

ROS provides a Qt based framework for developing dashboards for you robot called [rqt](http://wiki.ros.org/rqt). You can do this by organizing built-in [rqt](http://wiki.ros.org/rqt) plugins as well as your own Qt/ROS plugins into tabs and split screen layouts.

#### ROS Graph Viewer

Get an at a glance look at your robot's running ROS nodes and how they are connected. rqt_graph provides introspection and visualization of a live running ROS computational graph, allowing you to easily debug and understand your running system and how it is wired.

<img src="{{ site.baseurl }}/img/rqt_graph.png" style="float: left; width: 400px; padding-right: 10px; padding-bottom: 10px;" alt="rqt" />

#### Live Plotting Tool

Monitor output of encoders, voltages, or anything which can be represented as a float or integer over time using the built-in rqt_plot tool. rqt_plot leverages multiple plotting backend's (matplotlib, Qwt, pyqtgraph) so that you can choose which one best fits your needs.

<p style="color: red; font-size: 50px;">TODO: Image for rqt_plot</p>

#### Topic Tools

rqt comes with two topic related plugins, one lets you monitor and introspect any number of topics being published to on the system from one place. Another built-in tool allows you to publish content to any topic using simple Python snippets, allowing you to experiment with you system easily.

<p style="color: red; font-size: 50px;">TODO: Image for topic plugins</p>

#### Bag Tools

ROS provides logging and playback of data using rosbag's, and rqt provides a plugin which can record data to bags, play back selected topics and time frames of the bag, create new bags from a subset of another bag, and introspect the contents of a bag file, visualizing things like images and plotting of floats and integers over time.

<p style="color: red; font-size: 50px;">TODO: Image for rqt_bag</p>

# Integration with Other Libraries

Need to simulate your robot using OpenCV to detect and pickup an object using MoveIt!? ROS has got you covered, by providing interoperability with these libraries and a middleware capable of abstracting the location of data sources.

## Gazebo

Gazebo is a 3D indoor and outdoor multi-robot simulator, complete with dynamic and kinematic physics, and a pluggable physics engine. The Gazebo ROS plugin is capable of providing ROS interfaces for many existing robots and sensors which are nearly identical to the real device's ROS interfaces. These consistent interfaces allow you to write ROS nodes which work agnostic of whether the rest of the system is a simulated system, a recorded playback of a system, or the actual live system. This allows you to develop on simulated or recorded data and deploy to the real thing, with little to no changes in your code.

## OpenCV

OpenCV is the premier computer vision library, which is used in academia and products all over the world. OpenCV provides many common computer vision algorithms and utilities that you can build on to provide perception to your robot. ROS provides tight integration with OpenCV allowing users to easily convert between OpenCV images and ROS images for sending between processes. ROS also builds on OpenCV to provide systems like the (Image Pipeline)[http://wiki.ros.org/image_pipeline], which can be used for calibration, monocular processing, stereo processing, and depth processing. If your robot has cameras connected through USB, Firewire, or Ethernet, ROS and OpenCV are going to make your life easier.

## PCL

PCL, the Point Cloud Library, is a relatively new perception library focused on the manipulation and processing of three dimensional data and depth images. PCL provides a host of functionality for working with point clouds like filtering, feature detection, registration, kd-tree's and octree's, segmentation, sample_consensus, and more. If you are working with a three dimensional sensor like the Microsoft Kinect, or a scanning LiDAR then PCL and ROS will help you to collect, transform, process, and visualize that complex 3D data.

## MoveIt!

MoveIt! provides a framework for using and developing state of the art planning algorithms. Whether you are looking to leverage existing planning algorithms for motion planning of robotic arms or develop your own motion planning algorithm and comparing it against the existing system, MoveIt! will ease integration and provide powerful tools for debugging and interaction. MoveIt! is a first class citizen in ROS, as it uses much of ROS's abstract power to remain portable on top of various hardware and ROS's tools like Rviz for improving visibility and user experience. MoveIt! has a Rviz plugin, a rQt plugin, and integrates with the ROS Control framework which is used to implement control algorithms.

# Community Packages

Arguably one of the biggest strengths of ROS is the large and active community of users and collaborators. On top of the middleware, robot specific tools, and third party library integration, the ROS community has built many useful libraries and tools in many different robotics domains.


