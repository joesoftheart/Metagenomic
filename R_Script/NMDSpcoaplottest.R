args<-commandArgs(TRUE)
library(vegan)
library(ggplot2)
library(ellipse)
library(ggrepel)
MyMeta = data.frame(
  sites = c(1,2,3,4),
  type=c("soilsource1", "soilsource2", "soilsource3", "soilsource4"),
  row.names = "sites")
nmds<-read.table(file=args[1], header=T)
NMDS = data.frame(MDS1 = nmds$axis1, MDS2 = nmds$axis2, group=MyMeta$type)
group=MyMeta$type

png(args[2],width=8, height=6, units="in", res=300)
drawout=ggplot(data = NMDS, aes(MDS1, MDS2)) + geom_point(aes(color = group),size=2.5, alpha=0.5)+
  geom_hline(yintercept=0, linetype="dashed", size=.2) + geom_vline(xintercept=0, linetype="dashed", size=.2)+
  geom_point(aes(color = group),size=2.5, alpha=0.5)+
  scale_color_manual(labels = nmds$group, values=c("red","green3","orange","blue"))+
  guides(color=guide_legend("Source"))+
  geom_text_repel(aes(label = nmds$group), box.padding = unit(0.35, "lines"), segment.color=NA, size=3.5)+
  theme_bw()+
  theme(legend.position="right", legend.title=element_text(colour="black", size=10, face="bold"), legend.text = element_text(colour="black", size = 8, face = "plain"))
drawout
dev.off()
