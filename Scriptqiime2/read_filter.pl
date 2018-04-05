#!/usr/bin/perl

use warnings;
use strict;
use Parallel::ForkManager;
use File::Basename;
use Getopt::Long;
use Pod::Usage;

my $help; 
my $version_marker;
my $version = "2.0.2";

my $quality = 0;
my $percent = 0;
my $length = 0;
my $parallel;
my $keep;

my $forward = "ACGCGHNRAACCTTACC";
my $reverse = "TTGYACWCACYGCCCGT";
### (note that the above is the reverse complement of the below primer)
### my $reverse = "ACGGGCRGTGWGTRCAA";

my $primer_check = "none";
my $primer_trim;

my $out_dir = "filtered_reads";
my $log = "read_filter_log.txt";

my $bbmap_dir = "/usr/local/prg/bbmap";

my $res = GetOptions("out_dir|o=s" => \$out_dir,
		     "log=s"=>\$log,
		     "thread:i"=>\$parallel,
		     "min_quality|q=i" => \$quality,
		     "percent|p=i" => \$percent,
		     "min_length|l=i" => \$length,
		     "help|h"=>\$help,
		     "forward|f=s" => \$forward,
		     "reverse|r=s" => \$reverse,
		     "bbmap|b=s" => \$bbmap_dir,
		     "primer_check|c=s" => \$primer_check,
		     "primer_trim|t" => \$primer_trim,
		     "version|v" => \$version_marker,
		     "keep" => \$keep,
	  )	or pod2usage(2);

pod2usage(-verbose=>2) if $help;

if ( $version_marker )	{	print "version $version\n";	exit	}

if ( ( $quality == 0 ) or ( $percent == 0 ) or ( $length == 0 ) )	{
	die "min_quality, percent and min_length are required parameters that need non-zero interger values\nfor help type:	 perl read_filter.pl -h\n";
}

if ( ! -e "$bbmap_dir/bbduk.sh" )	{	die "$bbmap_dir/bbduk.sh does not exists, you may need to set the --bmap (-b) flag\n";	}

if ( index( `fastq_quality_filter -h` , "FASTX"  ) == -1 )	{	die "fastq_quality_filter is not in your path\n";	}

if ( $primer_check eq "none" )	{
	print STDERR "--primer_check set to \"none\", so not checking whether primer sequences are present\n";
} elsif ( $primer_check eq "both" )	{
	print STDERR "--primer_check set to \"both\", so checking for forward (5') primer $forward and reverse (3') primer $reverse. You can change the primer sequences to scan for with the --forward and --reverse options.\n";
} elsif ( $primer_check eq "forward" )	{
	print STDERR "--primer_check set to \"forward\", so checking for forward (5') primer $forward only. You can change the primer sequence to scan for with the --forward option.\n";
} else {
	die "--primer_check option needs to be either \"both\" or \"forward\" (default: both), the current setting of \"$primer_check\" is invalid.\n";
}

if ( ( $primer_trim ) and ( $primer_check eq "none" ) )	{	die "Primer trim option set, but primer check option not set so killing job\n"	}

my $cpu_count=1;
#if the option is set
if( defined( $parallel ) ){
    #option is set but with no value then use the max number of proccessors
    if( $parallel == 0 ){
	#load this module dynamically
	eval("use Sys::CPU;");
	$cpu_count=Sys::CPU::cpu_count();
    } else {
	$cpu_count=$parallel;
    }
}

system("mkdir -p $out_dir"); ### "-p" makes parent directories as needed

my @files=@ARGV;

pod2usage($0.': You must provide a list of fastq files to be filtered.') unless @files;

my @cmds = ();
my @rm_cmd = ();
my @tmpLog = ();

foreach my $path ( @files )	{
	
	my $file = basename( $path );
	
	my $ext;
	# first check whether filename matches has ".fastq" or ".fq" extension
	if ( $file =~ m/\.fastq$/ )	{
		$ext = "fastq";
	} elsif ( $file =~ m/\.fq$/ )	{
		$ext = "fq";
	} else {	die "file $path does not end in \".fastq\" or \".fq\"\n";	}

	my $outfile = $file;
	$outfile =~ s/\.$ext$/_filtered.$ext/;
	my $outfile_tmp1 = $outfile;
	my $outfile_tmp2 = $outfile;
	my $outfile_tmp3 = $outfile;
	my $outfile_tmp4 = $outfile;

	$outfile_tmp1 =~ s/\.$ext$/_TMP1.$ext/;
	$outfile_tmp2 =~ s/\.$ext$/_TMP2.$ext/;
	$outfile_tmp3 =~ s/\.$ext$/_TMP3.$ext/;	
	$outfile_tmp4 =~ s/\.$ext$/_TMP4.$ext/;	

	my $log_tmp = $outfile;
	$log_tmp =~ s/\.$ext$/_TMP_LOG.txt/;

	my $output = $out_dir . "/" . $outfile;
	my $output_tmp1 = $out_dir . "/" . $outfile_tmp1;
	my $output_tmp2 = $out_dir . "/" . $outfile_tmp2;
	my $output_tmp3 = $out_dir . "/" . $outfile_tmp3;
	my $output_tmp4 = $out_dir . "/" . $outfile_tmp4;

	my $log_tmp_out = $out_dir ."/" . $log_tmp;

	if ( -e $output )	{	die "output file $output already exists\n";	}

	my $f_l = length $forward;
	my $r_l = length $reverse;

	my $qFilterCmd = "fastq_quality_filter -v -Q33 -q $quality -p $percent -i $path -o $output_tmp1  >>$log_tmp_out";
	my $lFilterCmd = "$bbmap_dir/bbduk.sh -Xmx1g in=$output_tmp1 outu=$output_tmp2 minlength=$length 2>>$log_tmp_out";

	my $forwardPrimerCmd;
	my $reversePrimerCmd;
	my @tmp = ();
	
	my $trim_input = "";
	my $trim_options = "";
	
	if ( $primer_check eq "none" )	{
	
		$lFilterCmd = "$bbmap_dir/bbduk.sh -Xmx1g in=$output_tmp1 outu=$output minlength=$length 2>>$log_tmp_out";
		
		@tmp = ( $qFilterCmd , $lFilterCmd  );
		push ( @rm_cmd , ("rm $output_tmp1" ));
	
	} elsif ( $primer_check eq "both" )	{
	
		my $reverse_output;
		
		if ( $primer_trim )	{
			$reverse_output = $output_tmp4;
		} else {
			$reverse_output = $output;
		}
		
		$trim_input = $reverse_output;
		$trim_options = "forcetrimleft=$f_l forcetrimright2=$r_l";
	
		$forwardPrimerCmd = "$bbmap_dir/bbduk.sh -Xmx1g in=$output_tmp2 outm=$output_tmp3  restrictleft=$f_l k=$f_l literal=$forward mm=f rcomp=f copyundefined 2>>$log_tmp_out";
		$reversePrimerCmd = "$bbmap_dir/bbduk.sh -Xmx1g in=$output_tmp3 outm=$reverse_output  restrictright=$r_l k=$r_l literal=$reverse mm=f rcomp=f copyundefined 2>>$log_tmp_out";
		@tmp = ( $qFilterCmd , $lFilterCmd , $forwardPrimerCmd , $reversePrimerCmd  );
		push ( @rm_cmd , ("rm $output_tmp1" , "rm $output_tmp2" , "rm $output_tmp3" ));

	} elsif ( $primer_check eq "forward" )	{
		
		my $forward_output;
		
		if ( $primer_trim )	{
			$forward_output = $output_tmp3;
		} else {
			$forward_output = $output;
		}
		
		$trim_input = $forward_output;
		$trim_options = "forcetrimleft=$f_l";
		
		$forwardPrimerCmd = "$bbmap_dir/bbduk.sh -Xmx1g in=$output_tmp2 outm=$forward_output restrictleft=$f_l k=$f_l literal=$forward mm=f rcomp=f copyundefined 2>>$log_tmp_out";
		@tmp = ( $qFilterCmd , $lFilterCmd , $forwardPrimerCmd );
		push ( @rm_cmd , ("rm $output_tmp1" , "rm $output_tmp2")) ;
		
	} 
	
	if ( $primer_trim )	{
	
		my $primerTrimCmd = "$bbmap_dir/bbduk.sh -Xmx1g in=$trim_input out=$output $trim_options 2>>$log_tmp_out";
		
		push( @tmp , $primerTrimCmd );
		push( @rm_cmd, "rm $trim_input" );
	}
	
	push( @cmds , \@tmp );

	push( @tmpLog , "$log_tmp_out,$file" );
}

my $pm = new Parallel::ForkManager($cpu_count);
foreach my $cmds ( @cmds )	{

	$pm->start and next;

	my @c = @{$cmds};

	foreach my $c ( @c )	{
		print STDERR "running: $c\n\n";
		die if system( $c );
	}

	$pm->finish;
}
$pm->wait_all_children;

if ( ! $keep )	{
	foreach my $rm_cmd ( @rm_cmd )	{
			print STDERR "running: $rm_cmd\n\n";
			die if system( $rm_cmd );
	}
}

### parsing logfiles is not paralleled since writing to same file from mutliple jobs can screw up formatting
open( 'LOG' , '>' , $log ) or die "cant create LOG $log\n";
print LOG "file	initial	qFiltered	lFiltered	forwardFiltered	reverseFiltered	final	qFilteredPercent	lFilteredPercent	forwardFilteredPercent	reverseFilteredPercent	finalPercent\n";
foreach my $tmp ( @tmpLog )	{
	&add2log( $tmp );
}
close( 'LOG' );

sub add2log	{

	### take input raw, tmp log file and add it to the cleaned up global log file for all input files
	
	my @s = split( ',' , $_[0] );

	my $tmp = $s[0];
	my $name = $s[1];

	my @inCount = ();
	my @outCount = ();

	my $resultCount = 0;

	open( 'TMP' , '<' , $tmp ) or die "cant open TMP logfile $tmp\n";
	while( <TMP> )	{
		
		my @split = split( '\s+' , $_ );
		
		if ( ! exists $split[0] )	{	next	}

		if ( $split[0] eq "Input:" )	{
			push( @inCount , $split[1] );
		} elsif ( ( $split[0] eq "Contaminants:" ) or ( $split[0] eq "Output:" ) or ( $split[0] eq "Result:" ) )	{
			if ( $split[0] eq "Result:" )	{
				if ( $resultCount > 0 )	{
					next;
				} else {
					++$resultCount;
				}
			} else {}
			push( @outCount , $split[1] );
		} else {}
		
	} close( 'TMP' );
	
	my $initial = $inCount[0];
	my $qFiltered = $inCount[0] - $inCount[1];
	my $lFiltered = $inCount[1] - $outCount[1];
	
	my $forwardFiltered;
	my $forwardPercent;
	
	my $reverseFiltered;
	my $reversePercent;
	
	my $final;
	
	if ( $primer_check eq "none" )	{
	
		$forwardFiltered = "NA";
		$forwardPercent = "NA";
		
		$reverseFiltered = "NA";
		$reversePercent = "NA";

		$final = $outCount[1];

	} elsif ( $primer_check eq "both" )	{
		$forwardFiltered = $inCount[2] - $outCount[2];
		$forwardPercent = sprintf( "%.1f" , ($forwardFiltered / $initial)*100  );
		
		$reverseFiltered = $inCount[3] - $outCount[3];
		$final = $outCount[3];
		$reversePercent = sprintf( "%.1f" , ($reverseFiltered / $initial)*100  );
	} elsif ( $primer_check eq "forward" )	{
		$forwardFiltered = $inCount[2] - $outCount[2];
		$forwardPercent = sprintf( "%.1f" , ($forwardFiltered / $initial)*100  );
		
		$reverseFiltered = "NA";
		$final = $outCount[2];
		$reversePercent = "NA";
	}

	### note that percents are all based on initial count!
	my $qPercent = sprintf( "%.1f" , ($qFiltered / $initial)*100  );
	my $lPercent = sprintf( "%.1f" , ($lFiltered / $initial)*100  );

	my $finalPercent = sprintf( "%.1f" , ($final / $initial)*100 );
	
	print LOG "$name	$initial	$qFiltered	$lFiltered	$forwardFiltered	$reverseFiltered	$final	$qPercent	$lPercent	$forwardPercent	$reversePercent	$finalPercent\n";

	if ( ! $keep )	{
		system( "rm $tmp" );
	}
}

__END__

=head1 Name

read_filter.pl - wrapper to filter reads by quality with fastx and then by total length with bbmap. Reads without matches to the forward and reverse primers are then removed with bbmap. 

=head1 USAGE

read_filter.pl [-f <oligo> -r <oligo> -bbmap <directory> -log <logfile> -thread <#_CPU_to_use> -o <out_dir> -c <both|forward> -h --keep -t] -q <min_quality> -p <min_percent_sites_with_q> -l <min_length>  <list of fastq files>


Examples:


# remove all reads that do not have a quality score of 30 at least 90% of bases. Then remove all reads that are less than 400 bp long.

read_filter.pl -q 30 -p 90 -l 400 *.fastq


# thread with 2 CPUs, remove all reads that do not have a quality score of 30 at least 90% of bases. Then remove all reads that are less than 400 bp long.

read_filter.pl -thread 2 -q 30 -p 90 -l 400 *.fastq


# thread on all available CPUs, output into "filtered_reads", write log output to "filtered.log", min quality of 20, min percentage of bases with that quality of 90%, min length of 350 bases.

read_filter.pl -thread -o filtered_reads -log filtered.log -q 20 -p 80 -l 350 *.fastq


=head1 OPTIONS

=over 4

=item B<-h, --help>

Displays the entire help documentation.

=item B<-v, --version>

Displays script version and exits.

=item B<-o, --out_dir <file>>

Output directory for filtered fastq files. Default is "filtered_reads".

=item B<--thread <# of CPUs>>

Using this option without a value will use all CPUs on machine, while giving it a value will limit to that many CPUs. Without option only one CPU is used. 

=item B<--log <file>>

The location to write the log file. Default is "read_filter.log".
 
=item B<-q, --min_quality <int>>

Minimum base quality.

=item B<-p, --percent <int>>

Minimum percent of bases per read that pass quality cut-off

=item B<-l, --min_length <int>>

Minimum read length.

=item B<-f, --forward <sequence>>

Forward primer to match at beginning of all reads (IUPAC format, default: ACGCGHNRAACCTTACC).

=item B<-r, --reverse <sequence>>

Reverse primer to match at end of all reads (IUPAC format, default: TTGYACWCACYGCCCGT, which is the reverse complement of the primer ACGGGCRGTGWGTRCAA).

=item B<-b, --bbmap <path to directory>>

bbmap directory containing sh files (default: /usr/local/prg/bbmap). 

=item B<-c, --primer_check <[none|both|forward]>>

either "none", "both" or "forward", indicating whether not to check for primer sequences, to check both forward (5') and reverse (3') primer sequences or only the forward primer respectively (default: none).

=item B<-t, --primer_trim>

Flag to indicate that matched primers should also be trimmed off before writing filtered FASTQs. Not set by default (i.e. no trimming).  

=item B<--keep>

Flag to indicate that temporary files should not be deleted. Useful for troubleshooting.

=back

=head1 DESCRIPTION

B<read_filter.pl> This script automatically filters multiple fastqs by quality and length.

The script allows the use of multiple threads. 

By default, log output is written to "read_filter_log.txt".

bbmap is hard coded into this script, so this will have to changed on a different system (see "--bbmap" option). Also, FASTX-Toolkit needs to be installed and be in the user's $PATH.


# software websites:
http://sourceforge.net/projects/bbmap/
http://hannonlab.cshl.edu/fastx_toolkit/


=head1 AUTHOR

Gavin Douglas <gavin.douglas@dal.ca> (based on structure by Morgan Langille)

=cut

