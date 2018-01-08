args<-commandArgs(TRUE)
#rm(list=ls())
library(reshape2)
library(ggplot2)
library(scales)
library(gridExtra)
library(grid)

data=read.table(args[1], header=T, na.strings ="NA", sep="\t")
data1=melt(data,id.var="Level_2", measure.vars = c("Rel..freq..1....","Rel..freq..2...."))

p=ggplot(data=data1, aes(x=Level_2, y=value, fill=variable))+geom_bar(position=position_dodge(-.9),stat="identity")

p1=p+labs(x="", y="Mean proportion (%)") + scale_fill_manual(values=c("red","blue"), name="Source", labels=c("S1_1_16s_S1","S2_1_16s_S3"))+
  coord_flip()+theme_bw()+theme(plot.margin = unit(c(0.3,0,0,0), "lines"),legend.position='top', legend.direction='horizontal')+
  theme(axis.title.x = element_text(face="plain", hjust = 0.5, size=12))

p2=ggplot(data=data, aes(x=data$Level_2, y=data$Effect.size, ymin=data$X95.0..lower.CI, ymax=data$X95.0..upper.CI, colour = Effect.size >0))+
  geom_point()+geom_errorbar(width=0.3,position=position_dodge(.9)) +
  geom_text(aes(label=ifelse(data$p.values..corrected.<0.05, ifelse(data$p.values..corrected == 0.000000e+00, sprintf("%0.2f", round(data$p.values..corrected., digits=1)), as.character(format(data$p.values..corrected., digits=4, scientific = TRUE))),'')),hjust=-0.15, size =2.0)+
  scale_colour_manual(name='Effect.size >0', values = setNames(c('red','blue'),c(T,F)))+
  geom_hline(yintercept = 0, linetype="dashed", 
             color = "black", size=0.5)+
  coord_flip()+theme(axis.text.y = element_blank(), 
                     axis.ticks.y = element_blank(), 
                     #axis.title.y = element_blank(),
                     plot.margin = unit(c(2,2,0,0), "lines"),
                     plot.background = element_blank())+
  ggtitle("95% confidence intervals")+
  labs(x="", y="Difference between proportions (%)")+
  theme(plot.title = element_text(hjust = 0.5, size=12))+
  theme(axis.title.x = element_text(face="plain", hjust = 0.5, size=12))+
  theme(legend.position="none")

#tiff(args[2],width=10, height=6, units="in", res=300, compression = "lzw")
png(args[2],width=10, height=6, units="in", res=300)
grid.arrange(p1,p2,ncol=2)
dev.off()
####
